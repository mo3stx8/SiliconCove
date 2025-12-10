<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Account - Settings</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/account.css') }}" rel="stylesheet">
    <!-- website icon -->
    <link rel="icon" href="{{ asset('images/siliconcovelogo.png') }}" type="image/x-icon">
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            @include('my-account.includes.sidebar')

            <!-- Profile Settings Section -->
            <div class="col-md-9 main-container">
                <h2 class="mb-4">Profile Settings</h2>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Profile Picture Preview -->
                <div class="mb-3 text-center">
                    @if (auth()->user()->profile_picture)
                        <img src="{{ asset('storage/profile_images/' . auth()->user()->profile_picture) }}"
                            alt="Profile Picture" class="rounded-circle" width="120" height="120">

                        <!-- Remove Profile Picture Button (Separate Form) -->
                        <form action="{{ route('profile.removePicture') }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Remove Profile Picture</button>
                        </form>
                    @else
                        <img src="{{ asset('images/default-profile.jpg') }}" alt="Default Profile"
                            class="rounded-circle" width="120" height="120">
                    @endif
                </div>

                <!-- Profile Update Form -->
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ auth()->user()->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ auth()->user()->email }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone (+967)</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                            value="{{ auth()->user()->phone }}" pattern="7[0-9]{8}" placeholder="7XXXXXXXX"
                            maxlength="9" title="Please enter a valid Yemini phone number starting with 7" required>
                        <div class="form-text">Enter a valid Yemini phone number (e.g., 774316974)</div>
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    </div>

                    <button type="submit" class="btn btn-success w-100">Update Profile</button>
                </form>
            </div>


        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
