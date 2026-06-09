@env('local')
    <script>
        (function() {
            const endpoint = @json(url('/__dev/hot-reload'));
            let last = null;
            let polling = false;

            function refreshLivewire() {
                if (typeof Livewire === 'undefined') {
                    window.location.reload();
                    return;
                }

                if (typeof Livewire.all === 'function') {
                    Livewire.all().forEach(function(component) {
                        component.$wire.call('$refresh');
                    });

                    return;
                }

                if (Livewire.components?.componentsById) {
                    Object.values(Livewire.components.componentsById).forEach(function(component) {
                        component.call('$refresh');
                    });

                    return;
                }

                window.location.reload();
            }

            async function poll() {
                if (polling || document.hidden) {
                    return;
                }

                polling = true;

                try {
                    const response = await fetch(endpoint, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        cache: 'no-store',
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();

                    if (last === null) {
                        last = data;
                        return;
                    }

                    if (data.layout !== last.layout) {
                        window.location.reload();
                        return;
                    }

                    if (data.livewire !== last.livewire) {
                        refreshLivewire();
                        console.log('[dev] livewire hot updated.');
                    }

                    last = data;
                } catch (error) {
                    // Ignore transient network errors while polling.
                } finally {
                    polling = false;
                }
            }

            function start() {
                poll();
                setInterval(poll, 1500);
                console.log('[dev] livewire hot reload ready.');
            }

            document.addEventListener('livewire:init', start, {
                once: true
            });
        })();
    </script>
@endenv
