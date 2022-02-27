<table>
    <tr>
        <th>id</th>
        <th>type</th>
        <th>command</th>
        <th>mutex_cache_key</th>
        <th>command output</th>
        <th>logged_at</th>
    </tr>
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