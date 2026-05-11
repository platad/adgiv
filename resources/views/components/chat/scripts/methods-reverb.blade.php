{{-- resources/views/components/chat/scripts/methods-reverb.blade.php --}}
subscribeToReverb() {
    if (!window.__pusher) return;
    this.channel = window.__pusher.subscribe(`chat-session.${this.sessionId}`);
    this.channel.bind('agent.status.updated', (data) => {
        if (!this.workflowBubble) this.workflowBubble = { label: 'Analisis Agen Berlangsung...' };
        
        // Update global progress state using snake_case from broadcastWith
        if (data.score !== undefined) this.tendencyScore = data.score;
        if (data.round !== undefined) this.currentRound = data.round;
        if (data.total_rounds !== undefined) this.totalRounds = data.total_rounds;
        if (data.message) this.workflowBubble.label = data.message;

        const agent = this.agents.find(a => a.name === data.agent_name);
        if (agent) agent.status = data.status;
        
        if (data.is_argument && data.result_data) {
            this.messages.push({
                id: Date.now() + Math.random(), 
                role: 'assistant',
                content: `**[${data.display_name} - Ronde ${data.round}]**: ${data.result_data.reasoning}`,
                metadata: { agent_name: data.display_name, round: data.round },
                created_at: new Date()
            });
        }
        this.$nextTick(() => this.scrollToBottom());
    });
    this.channel.bind('final.decision.reached', (data) => {
        this.isAnalysing = false;
        setTimeout(() => { this.workflowBubble = null; this.resetAgents(); }, 1000);
        this.loadMessages();
    });
},
