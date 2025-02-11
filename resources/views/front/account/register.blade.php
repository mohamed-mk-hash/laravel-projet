@extends('front.layouts.app')

@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">Register</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            <div class="login-form">    
                <form action="" method="post" name="registrationForm" id="registrationForm">
                    @csrf
                    <h4 class="modal-title">Register Now</h4>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name" id="name" name="name">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Email" id="email" name="email">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Phone" id="phone" name="phone">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                        <p></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Confirm Password" id="password_confirmation" name="password_confirmation">
                        <p></p>
                    </div>
                    <div class="form-group small">
                        <a href="#" class="forgot-link">Forgot Password?</a>
                        <p></p>
                    </div> 
                    <button type="submit" class="btn btn-dark btn-block btn-lg" value="Register">Register</button>
                </form>			
                <div class="text-center small">Already have an account? <a href="{{ route('account.login') }}">Login Now</a></div>
            </div>
        </div>
    </section>
</main>
@endsection

@section('customJs')
    <script type="text/javascript">
        $("#registrationForm").submit(function(event){
            event.preventDefault();
            $('button[type="submit"]').prop('disabled,true');

            $.ajax({
                url: '{{ route("account.processRegister") }}',
                type: 'post',
                data: $(this).serializeArray(),
                datatype: 'json',
                success: function(response){
                    $('button[type="submit"]').prop('disabled,false');
                    var errors = response.errors;

                    // Clear previous error messages and styles
                    $("input").siblings("p").removeClass('invalid-feedback').html('');
                    $("input").removeClass('is-invalid');

                    if (response.status == false) {
                        // Handle validation errors
                        if (errors) {
                            if (errors.name) {
                                $("#name").siblings("p").addClass('invalid-feedback').html(errors.name[0]);
                                $("#name").addClass('is-invalid');
                            }
                            
                            if (errors.email) {
                                $("#email").siblings("p").addClass('invalid-feedback').html(errors.email[0]);
                                $("#email").addClass('is-invalid');
                            }
                            
                            if (errors.password) {
                                $("#password").siblings("p").addClass('invalid-feedback').html(errors.password[0]);
                                $("#password").addClass('is-invalid');
                            }
                        }
                    } else {
                            
                        // $("#name").siblings("p").addClass('invalid-feedback').html(errors.name[0]);
                        // $("#name").addClass('is-invalid');

                        // $("#email").siblings("p").addClass('invalid-feedback').html(errors.email[0]);
                        // $("#email").addClass('is-invalid');

                        // $("#password").siblings("p").addClass('invalid-feedback').html(errors.password[0]);
                        // $("#password").addClass('is-invalid');

                        // Redirect to the login page upon successful registration
                        window.location.href = "{{ route('account.login') }}";
                    }
                },
                error: function(jqXHR, exception){
                    console.log("Something went wrong");
                }
            });
        });
    </script>
@endsection



