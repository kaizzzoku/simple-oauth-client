@extends('base')

@section('content')
	<div class="card">
		<div class="card-header">
			<a href="{{ route('me.profile') }}">{{ auth()->user()->name }}</a>: Answers
		</div>
		<div class="card-body">
			@empty($answers)
				The user has no answers yet
			@else
				@foreach($answers as $answer)
					<div class="card mb-2">
						<div class="card-header">
							Question: <a href="{{ env('RESOURCE_SERVER').'/questions/'.$answer['question_id'] }}">{{ env('RESOURCE_SERVER').'/questions/'.$answer['question_id'] }}</a>
						</div>
						<div class="card-body">
							{{ $answer['body'] }}
						</div>
						<div class="card-footer">
							Answered: {{ $answer['created_at'] }}
						</div>
					</div>
				@endforeach
			@endempty
		</div>
	</div>
@endsection