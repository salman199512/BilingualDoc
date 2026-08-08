@extends('layouts.app')

@section('page-title')
    My Profile & Settings
@endsection

@section('content')
<div class="dashboard-grid animate-fade-in">
    <!-- Left Column: Profile Details -->
    <div>
        <div class="card">
            <h3 class="card-title">Profile Information</h3>
            <form action="{{ route('profile.update') }}" method="POST" onsubmit="showLoader()">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-input" value="{{ old('first_name', $user->first_name) }}" placeholder="John">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-input" value="{{ old('last_name', $user->last_name) }}" placeholder="Doe">
                    </div>
                </div>

                <div class="form-group">
                    <label for="office_name">Office / Organization Name</label>
                    <input type="text" name="office_name" id="office_name" class="form-input" value="{{ old('office_name', $user->office_name) }}" placeholder="e.g. High Court of Gujarat">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="+91 99999 99999">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required placeholder="john.doe@bilingual.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Postal Address</label>
                    <textarea name="address" id="address" class="form-input" rows="4" placeholder="Enter complete office or residential address...">{{ old('address', $user->address) }}</textarea>
                </div>

                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Change Password -->
    <div>
        <div class="card">
            <h3 class="card-title">Change Password</h3>
            <form action="{{ route('profile.password') }}" method="POST" onsubmit="showLoader()">
                @csrf
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-input" required placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-input" required placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-input" required placeholder="••••••••">
                </div>

                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
