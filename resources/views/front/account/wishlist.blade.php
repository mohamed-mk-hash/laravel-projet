@extends('front.layouts.app')

@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Wishlist</a></li>
                    <li class="breadcrumb-item">Wishlist</li>
                </ol>
            </div>
        </div>
    </section>
        
    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                @include('front.account.common.message')
                <div class="col-md-3">

                    @include('front.account.common.sidebare')
                    
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">My Wishlist</h2>
                        </div>
                        <div class="card-body p-4">
                            @if ($wishlists->isNotEmpty())
                                @foreach ($wishlists as $wishlist)
                                    <div class="d-sm-flex justify-content-between mt-lg-4 mb-4 pb-3 pb-sm-2 border-bottom">
                                        <div class="d-block d-sm-flex align-items-start text-center text-sm-start">
                                           
                                            <a class="d-block flex-shrink-0 mx-auto me-sm-4" href="{{ route('front.product',$wishlist->product->slug) }}" style="width: 10rem;">
                                                {{-- <img src="images/product-1.jpg" alt="Product"> --}}
                                                @php
                                                    $productImage = getProductImage($wishlist->product_id)
                                                @endphp

                                                @if (!empty($productImage))
                                                    <img src="{{ asset('uploads/product/small/'.$productImage->image) }}"  > 
                                                @else
                                                    <img src="{{ asset('admin-assets/img/default-150x150.png') }}"  >                                            
                                                @endif
                                            </a>

                                        
                                            <div class="pt-2">
                                                <h3 class="product-title fs-base mb-2"><a href="{{ route('front.product',$wishlist->product->slug) }}">{{ $wishlist->product->titel }}</a></h3>                                        
                                                <div class="fs-lg text-accent pt-2">
                                                    <span class="h5"><strong>${{ $wishlist->product->price }}</strong></span>
                                                    @if ($wishlist->product->compare_price > 0)
                                                        <span class="h6 text-underline"><del>${{ $wishlist->product->compare_price }}</del></span>                                            
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pt-2 ps-sm-3 mx-auto mx-sm-0 text-center">
                                            <button onclick="removeProduct({{ $wishlist->product->id }})" class="btn btn-outline-danger btn-sm" type="button"><i class="fas fa-trash-alt me-2"></i>Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="h5">Your Wishlist is Empty!!</div>
                            @endif
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@section('customJs')
    <script>
        function removeProduct(id){
            $.ajax({
			url: '{{ route("account.removeProductFromWishlist") }}',
			type: 'post',
			data: {id:id},
			dataTypa: 'json',
			success: function(response) {
				if(response.status == true){
					window.location.href = "{{ route('account.wishlist') }}"; 
				}
			}
		});
        }
    </script>
@endsection