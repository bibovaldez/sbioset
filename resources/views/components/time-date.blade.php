<div>
    <div aria-live="polite" aria-atomic="true">
        <div id="digital-clock" class="font-semiboldtext-gray-900 dark:text-gray-100 px-3"></div>
    </div>
    <script>
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds} ${ampm}`;

            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateString = now.toLocaleDateString(undefined, options);

            document.getElementById('digital-clock').textContent = `${dateString} ${timeString}`;
        }

        setInterval(updateClock, 1000);
        updateClock(); // Initial call to display clock immediately
    </script>
</div>
