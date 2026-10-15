<?php

namespace App\Services;

class GraphAnalyticsService
{
    /**
     * Compute comprehensive graph centrality & interaction topology metrics.
     *
     * @param array $graphData Contains 'nodes' and 'edges'
     * @param array $transcription Array of transcription blocks
     * @return array
     */
    public function calculateMetrics(array $graphData, array $transcription = []): array
    {
        $rawNodes = $graphData['nodes'] ?? [];
        $rawEdges = $graphData['edges'] ?? [];

        // 1. Ekstrak Simpul Unik
        $nodes = [];
        foreach ($rawNodes as $node) {
            $id = trim($node['id'] ?? ($node['name'] ?? ''));
            if ($id === '') continue;
            $nodes[$id] = [
                'id' => $id,
                'name' => $node['name'] ?? ($node['label'] ?? $id),
                'category' => $node['category'] ?? ($node['type'] ?? 'entity'),
            ];
        }

        // Pastikan simpul dari edges juga tercatat jika belum ada di node list
        foreach ($rawEdges as $edge) {
            $src = trim($edge['source'] ?? ($edge['from'] ?? ''));
            $tgt = trim($edge['target'] ?? ($edge['to'] ?? ''));
            if ($src !== '' && !isset($nodes[$src])) {
                $nodes[$src] = ['id' => $src, 'name' => $src, 'category' => 'entity'];
            }
            if ($tgt !== '' && !isset($nodes[$tgt])) {
                $nodes[$tgt] = ['id' => $tgt, 'name' => $tgt, 'category' => 'entity'];
            }
        }

        $nodeCount = count($nodes);

        if ($nodeCount === 0) {
            return $this->getEmptyMetrics();
        }

        // 2. Inisialisasi struktur derajat dan adjacency
        $inDegree = array_fill_keys(array_keys($nodes), 0);
        $outDegree = array_fill_keys(array_keys($nodes), 0);
        $weightedDegree = array_fill_keys(array_keys($nodes), 0);
        $adjList = array_fill_keys(array_keys($nodes), []);
        $edgePairs = []; // key: "src->tgt" => weight

        $totalEdgesCount = 0;
        $totalWeight = 0;

        foreach ($rawEdges as $edge) {
            $src = trim($edge['source'] ?? ($edge['from'] ?? ''));
            $tgt = trim($edge['target'] ?? ($edge['to'] ?? ''));
            if ($src === '' || $tgt === '' || !isset($nodes[$src]) || !isset($nodes[$tgt])) {
                continue;
            }

            $weight = (float)($edge['value'] ?? ($edge['weight'] ?? 1));
            if ($weight <= 0) $weight = 1;

            $outDegree[$src]++;
            $inDegree[$tgt]++;
            $weightedDegree[$src] += $weight;
            $weightedDegree[$tgt] += $weight;

            if (!in_array($tgt, $adjList[$src])) {
                $adjList[$src][] = $tgt;
            }

            $pairKey = $src . '->' . $tgt;
            $edgePairs[$pairKey] = ($edgePairs[$pairKey] ?? 0) + $weight;
            $totalEdgesCount++;
            $totalWeight += $weight;
        }

        // 3. Hitung Degree Centrality (Normalized)
        // C_D(v) = (in_deg + out_deg) / (N - 1)
        $normFactor = $nodeCount > 1 ? ($nodeCount - 1) : 1;
        $degreeCentrality = [];
        foreach ($nodes as $id => $info) {
            $totalDeg = $inDegree[$id] + $outDegree[$id];
            $degreeCentrality[$id] = [
                'id' => $id,
                'name' => $info['name'],
                'in_degree' => $inDegree[$id],
                'out_degree' => $outDegree[$id],
                'total_degree' => $totalDeg,
                'weighted_degree' => $weightedDegree[$id],
                'score' => round($totalDeg / $normFactor, 4),
            ];
        }

        // 4. Hitung Betweenness Centrality (Brandes' Algorithm untuk Graf Berarah)
        $betweenness = array_fill_keys(array_keys($nodes), 0.0);
        $nodeKeys = array_keys($nodes);

        foreach ($nodeKeys as $s) {
            $stack = [];
            $pred = array_fill_keys($nodeKeys, []);
            $sigma = array_fill_keys($nodeKeys, 0);
            $sigma[$s] = 1;
            $dist = array_fill_keys($nodeKeys, -1);
            $dist[$s] = 0;

            $queue = new \SplQueue();
            $queue->enqueue($s);

            while (!$queue->isEmpty()) {
                $v = $queue->dequeue();
                $stack[] = $v;

                foreach ($adjList[$v] as $w) {
                    if ($dist[$w] < 0) {
                        $dist[$w] = $dist[$v] + 1;
                        $queue->enqueue($w);
                    }
                    if ($dist[$w] === $dist[$v] + 1) {
                        $sigma[$w] += $sigma[$v];
                        $pred[$w][] = $v;
                    }
                }
            }

            $delta = array_fill_keys($nodeKeys, 0.0);
            while (!empty($stack)) {
                $w = array_pop($stack);
                foreach ($pred[$w] as $v) {
                    if ($sigma[$w] > 0) {
                        $delta[$v] += ($sigma[$v] / $sigma[$w]) * (1.0 + $delta[$w]);
                    }
                }
                if ($w !== $s) {
                    $betweenness[$w] += $delta[$w];
                }
            }
        }

        // Normalisasi Betweenness untuk Directed Graph: / ((N-1)(N-2))
        $bNormFactor = ($nodeCount > 2) ? (($nodeCount - 1) * ($nodeCount - 2)) : 1;
        $betweennessCentrality = [];
        foreach ($nodes as $id => $info) {
            $score = $betweenness[$id] / $bNormFactor;
            $betweennessCentrality[$id] = [
                'id' => $id,
                'name' => $info['name'],
                'raw_score' => round($betweenness[$id], 2),
                'score' => round($score, 4),
            ];
        }

        // 5. Hitung Graph Density (Kerapatan Graf)
        // Density = |E| / (N * (N - 1))
        $possibleEdges = $nodeCount > 1 ? ($nodeCount * ($nodeCount - 1)) : 1;
        $uniqueDirectedEdges = count($edgePairs);
        $graphDensity = round($uniqueDirectedEdges / $possibleEdges, 4);

        // 6. Hitung Reciprocity Index (Derajat Timbal Balik Ujaran)
        // Reciprocity = (2 * jumlah pasangan berarah timbal balik) / total pasangan
        $mutualEdges = 0;
        $checked = [];
        foreach ($edgePairs as $pair => $w) {
            [$src, $tgt] = explode('->', $pair);
            $reverse = $tgt . '->' . $src;
            if (isset($edgePairs[$reverse]) && !isset($checked[$pair]) && !isset($checked[$reverse])) {
                $mutualEdges += 2;
                $checked[$pair] = true;
                $checked[$reverse] = true;
            }
        }
        $reciprocity = $uniqueDirectedEdges > 0 ? round($mutualEdges / $uniqueDirectedEdges, 4) : 0.0;

        // 7. Cari Key Players & Topik Jembatan (Bridge Entities)
        // Urutkan Degree Centrality
        $sortedDegree = $degreeCentrality;
        uasort($sortedDegree, fn($a, $b) => $b['score'] <=> $a['score']);
        $dominantActor = !empty($sortedDegree) ? reset($sortedDegree) : null;

        // Urutkan Betweenness (Bridge Entities)
        $sortedBetweenness = $betweennessCentrality;
        uasort($sortedBetweenness, fn($a, $b) => $b['score'] <=> $a['score']);
        $topBridge = !empty($sortedBetweenness) ? reset($sortedBetweenness) : null;

        // 8. Klasifikasi Dinamika Relasi Berdasarkan Metrik Ilmiah
        $interactionCharacter = 'Kondusif & Dialogis';
        if ($reciprocity >= 0.5) {
            $interactionCharacter = 'Dialogis Kolaboratif Kuat (Kemitraan)';
        } elseif ($reciprocity >= 0.25) {
            $interactionCharacter = 'Dialogis Seimbang Terarah';
        } else {
            $interactionCharacter = 'Asimetris Direktif (Dominasi Satu Arah)';
        }

        // 9. Identifikasi Hubungan Timbal Balik Khusus Dosen <-> Mahasiswa
        $dosenMhsTurns = 0;
        $mhsDosenTurns = 0;
        foreach ($edgePairs as $pair => $w) {
            [$src, $tgt] = explode('->', $pair);
            $srcLower = strtolower($src);
            $tgtLower = strtolower($tgt);
            $isSrcDosen = (str_contains($srcLower, 'dosen') || str_contains($srcLower, 'supervisor') || str_contains($srcLower, 'penguji'));
            $isTgtMhs = (str_contains($tgtLower, 'mhs') || str_contains($tgtLower, 'mahasiswa') || str_contains($tgtLower, 'student'));
            $isSrcMhs = (str_contains($srcLower, 'mhs') || str_contains($srcLower, 'mahasiswa') || str_contains($srcLower, 'student'));
            $isTgtDosen = (str_contains($tgtLower, 'dosen') || str_contains($tgtLower, 'supervisor') || str_contains($tgtLower, 'penguji'));

            if ($isSrcDosen && $isTgtMhs) $dosenMhsTurns += $w;
            if ($isSrcMhs && $isTgtDosen) $mhsDosenTurns += $w;
        }

        return [
            'summary' => [
                'node_count' => $nodeCount,
                'edge_count' => $totalEdgesCount,
                'unique_edges_count' => $uniqueDirectedEdges,
                'graph_density' => $graphDensity,
                'density_percentage' => round($graphDensity * 100, 1),
                'reciprocity_index' => $reciprocity,
                'reciprocity_percentage' => round($reciprocity * 100, 1),
                'interaction_character' => $interactionCharacter,
                'dominant_actor' => $dominantActor,
                'top_bridge' => $topBridge,
                'dosen_to_mhs_weight' => $dosenMhsTurns,
                'mhs_to_dosen_weight' => $mhsDosenTurns,
            ],
            'degree_centrality' => array_values($sortedDegree),
            'betweenness_centrality' => array_values($sortedBetweenness),
            'nodes_map' => $nodes,
        ];
    }

    /**
     * Fallback for empty graph data.
     */
    protected function getEmptyMetrics(): array
    {
        return [
            'summary' => [
                'node_count' => 0,
                'edge_count' => 0,
                'unique_edges_count' => 0,
                'graph_density' => 0.0,
                'density_percentage' => 0,
                'reciprocity_index' => 0.0,
                'reciprocity_percentage' => 0,
                'interaction_character' => 'Belum ada data interaksi',
                'dominant_actor' => null,
                'top_bridge' => null,
                'dosen_to_mhs_weight' => 0,
                'mhs_to_dosen_weight' => 0,
            ],
            'degree_centrality' => [],
            'betweenness_centrality' => [],
            'nodes_map' => [],
        ];
    }
}
