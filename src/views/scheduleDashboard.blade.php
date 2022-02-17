<table>
    @foreach ($data as $item)
    <tr>
        <td>{{ $item->id }}</td>
        <td>{{ $item->type }}</td>
        <td>{{ $item->command }}</td>
        <td>{{ $item->mutex_cache_key }}</td>
        <td>{{ $item->output }}</td>
        <td>{{ $item->logged_at }}</td>
    <tr>
        @endforeach
</table>