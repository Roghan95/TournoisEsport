const eventSource = new EventSource("{{ mercure('https://example.com/books/1')|escape('js') }}");
eventSource.onmessage = event => {
    // Will be called every time an update is published by the server
    console.log(JSON.parse(event.data));
}