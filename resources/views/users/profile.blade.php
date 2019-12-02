@extends('base')


@section('content')
	<div class="card">
		<div class="card-header">
			{{ $user->name }}
		</div>
		<div class="card-body">
			<a href="{{ route('me.answers') }}">Answers</a><br>
			<a href="{{ route('me.questions') }}">Questions</a>
		</div>
	</div>
@endsection