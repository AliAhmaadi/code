@extends('layout.master')
@section('page-title')
	Gallery
@endsection
@section('main-content')
<main class="main-content">

		<!-- Gallery -->
        <div class="gallery tc-padding">
      		<div class="container">
      			<div class="row no-gutters">
      				@forelse($galleries as $gallery)
      				<div class="col-lg-3 col-xs-6 r-full-width">
      					<div class="gallery-figure style-2"> 
	                @if($gallery->media_img == 'No image found')
										<img src="/assets/images/no-img.jpg" width="283" height="283" alt="No image found">
									@else
										<img src="/uploads/{{ $gallery->media_img }}" width="283" height="283" alt="{{ $gallery->fullname }}">
									@endif
              		<div class="overlay">
              			<ul class="position-center-x"></ul>
              		</div>
	              </div>
      				</div>
      				@empty
								<div class="alert alert-danger">No record found!</div>
							@endforelse
      				<div class="col-xs-12">
		           	{{ $galleries->links() }}
		           	</div>
      				</div>
      			</div>
          </div>
      	</div>
		<!-- Gallery -->

	</main>
@endsection