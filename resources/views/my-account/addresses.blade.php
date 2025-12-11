<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Account - Adresses</title>

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

            <!-- Addresses Form -->
            <div class="col-md-9 main-container">
                <h2 class="mb-4">My Addresses</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('addresses.storeOrUpdate') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Enter address"
                            value="{{ old('address', $address->address ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Area</label>
                        <input type="text" name="area" class="form-control" placeholder="Enter area"
                            value="{{ old('area', $address->area ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Region</label>
                        <input type="text" name="region" class="form-control" placeholder="Enter region"
                            value="{{ old('region', $address->region ?? '') }}" required>
                    </div>

                    {{-- <div class="mb-3">
                        <label class="form-label">Zip Code</label>
                        <input type="text" name="zip_code" class="form-control" placeholder="Enter zip code"
                            value="{{ old('zip_code', $address->zip_code ?? '') }}" required>
                    </div> --}}
                    {{-- the zip code is commeted to be as feature working on it later when moving to international shipping --}}

                    <button type="submit"
                        class="btn btn-success w-100">{{ $address ? 'Update Address' : 'Save Address' }}</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
