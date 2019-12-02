@extends('base')

@section('content')
	<div class="card">
		<div class="card-header">
			<a href="{{ route('me.profile') }}">{{ auth()->user()->name }}</a>: Questions
		</div>
		<div class="card-body">
			@empty($questions)
				The user has no questions yet
			@else
				@foreach($questions as $question)
					<div class="card mb-2">
						<div class="card-header">
							<a href="{{ env('RESOURCE_SERVER').'/questions/'.$question['id'] }}">{{ $question['title'] }}</a>
						</div>
						<div class="card-body">
							{{ $question['description'] }}
						</div>
						<div class="card-footer">
							Asked: {{ $question['created_at'] }}
						</div>
					</div>
				@endforeach
			@endempty
		</div>
	</div>
@endsection