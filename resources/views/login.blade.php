@extends('base')

@section('content')
	<div class="card">
		<a href="{{ route('oauth.redirect') }}" class="btn btn-primary">
			Login with Q&A Service
		</a>	
	</div>

@endsection
