<div>
    <h2>タイトル</h2>
    <p>{{ $inquiry->title }}</p>
    <br>
    <h2>詳細</h2>
    <div>{!! nl2br(e($inquiry->detail)) !!}</div>
</div>