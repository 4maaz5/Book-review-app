@extends('layouts.app')
@section('main')


<div class="container">
<div class="row my-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-lg">
            <div class="card-header  text-white">
                Welcome, John Doe
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="images/profile-img-1.jpg" class="img-fluid rounded-circle" alt="Luna John">
                </div>
                <div class="h5 text-center">
                    <strong>John Doe</strong>
                    <p class="h6 mt-2 text-muted">5 Reviews</p>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-lg mt-3">
            <div class="card-header  text-white">
                Navigation
            </div>
            <div class="card-body sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="book-listing.html">Books</a>
                    </li>
                    <li class="nav-item">
                        <a href="reviews.html">Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a href="profile.html">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a href="my-reviews.html">My Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a href="change-password.html">Change Password</a>
                    </li>
                    <li class="nav-item">
                        <a href="login.html">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">

        <div class="card border-0 shadow">
            <div class="card-header  text-white">
                Edit Review
            </div>
            <div class="card-body pb-3">
                <form action="{{ route('account.reviews.update',$review->id) }}" method="post" >
                    @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Review</label>
                    <textarea class="form-control @error('review')
                         is-invalid
                    @enderror" name="review" placeholder="Review" id="review">{{ old('review',$review->review) }}</textarea>
                    @error('review')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control @error('status')
                        is-invalid
                    @enderror" name="status" id="status">
                        <option value="1" {{ ($review->status==1)?'selected':'' }}>Active</option>
                        <option value="0" {{ ($review->status==0)?'selected':'' }}>Block</option>
                    </select>
                    @error('status')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
                </div>

                <button class="btn btn-primary mt-2">Update</button>
                </form>
            </div>

        </div>
    </div>
</div>
</div>

@endsection
