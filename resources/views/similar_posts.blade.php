<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Similar Posts</title>
</head>
<body>
<h1>Similar Posts Found</h1>

@foreach ($similarPosts as $posts)
    <div>
        <h2>Channel 1 Post</h2>
        <p>{{ $posts['channel_1_post'] }}</p>

        <h2>Channel 2 Post</h2>
        <p>{{ $posts['channel_2_post'] }}</p>
    </div>
    <hr>
@endforeach
</body>
</html>
