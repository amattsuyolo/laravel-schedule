<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Schedule Dashboard</title>
</head>

<body>
    <div class="container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <!-- <th scope="col">id</th> -->
                    <!-- <th scope="col">type</th> -->
                    <th scope="col">command</th>
                    <!-- <th scope="col">command output</th> -->
                    <th scope="col">next_run_at</th>
                    <th scope="col">last_starting_logged_at</th>
                    <th scope="col">state</th>
                    <th scope="col">msg</th>
                    <th scope="col">mutex_cache_key</th>
                </tr>
            </thead>
            @foreach ($data as $item)
            <tbody>
                <tr>
                    <!-- <td>{{ $item->id }}</td> -->
                    <!-- <td>{{ $item->type }}</td> -->
                    <td><a href="/schedule-assistant-dashboard/{{$item->command}}">{{ $item->command }}</a></td>
                    <!-- <td>{{ $item->output }}</td> -->
                    <td>{{ $item->nextRunAt ?? ""}}</td>
                    <td>{{ $item->logged_at ?? ""}}</td>
                    <td>{{ $item->state ?? ""}}</td>
                    <td>{{ $item->msg ?? ""}}</td>
                    <td>{{ $item->mutex_cache_key ?? ""}}</td>
                <tr>
                    @endforeach
            <tbody>
        </table>
    </div>


    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>