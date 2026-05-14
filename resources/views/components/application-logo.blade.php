{{-- resources/views/components/application-logo.blade.php --}}
<svg {{ $attributes }} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    {{-- Primary Sparkle (Large) --}}
    <path d="M12 2C12 7.52285 16.4772 12 22 12C16.4772 12 12 16.4772 12 22C12 16.4772 7.52285 12 2 12C7.52285 12 12 7.52285 12 2Z" 
          fill="currentColor"/>
    
    {{-- Secondary Sparkle (Medium-Small, Top Right) --}}
    <path d="M19 3C19 5.20914 20.7909 7 23 7C20.7909 7 19 8.79086 19 11C19 8.79086 17.2091 7 15 7C17.2091 7 19 5.20914 19 3Z" 
          fill="currentColor" class="opacity-70"/>

    {{-- Tertiary Sparkle (Small, Bottom Left) --}}
    <path d="M5 14C5 15.6569 6.34315 17 8 17C6.34315 17 5 18.3431 5 20C5 18.3431 3.65685 17 2 17C3.65685 17 5 15.6569 5 14Z" 
          fill="currentColor" class="opacity-50"/>
</svg>
