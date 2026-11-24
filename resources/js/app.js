import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('countUp', (target) => ({
    value: 0,

    init() {
        const finalValue = Math.max(0, Number(target) || 0);

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.value = finalValue;
            return;
        }

        const duration = 800;
        const startTime = performance.now();
        const animate = (currentTime) => {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            this.value = Math.round(finalValue * progress);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    },
}));

Alpine.start();
