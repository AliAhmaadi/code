@extends('layout.master')
@section('page-title')
	Checkout
@endsection
@section('main-content')
<main class="main-content new-section">

<div class="container">
    <table id="cart" class="table table-hover table-condensed">
        <thead>
            <tr>
                <th style="width:50%">Product</th>
                <th style="width:10%">Price</th>
                <th style="width:8%">Quantity</th>
                <th style="width:22%" class="text-center">Subtotal</th>
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody id="cart_summary">
           <tr>
                  <td data-th="Product">
                      <div class="row">
                          <div class="col-sm-12">
                              <h4 class="nomargin">Loading...</h4>
                          </div>
                      </div>
                  </td>
              </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right" id="cart_total"><h3><strong>Total $0.00</strong></h3></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right">
                    <div class="row">
                    	<div class="col-6">
                    		<form action="{{ route('payment.process') }}" method="POST" id="payment-form">
                              @csrf
                              <input type="text" class="form-control" name="name" placeholder="Name on Card">
                              <div id="card-element"></div>
                              <button class="btn btn-primary" type="submit">Submit Payment</button>
                          </form>
                    	</div>
                      <div class="col-6">
                        <a href="{{ url('/') }}" class="btn btn-warning"><i class="fa fa-angle-left"></i> Continue Shopping</a>
                      </div>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
</main>
@endsection
@section('scripts')
<script type="text/javascript">
	var cartItems = JSON.parse(localStorage.getItem('cart')) || [];
initSummary(cartItems); 

function  initSummary(cartItems) {
    htmlContent = "";

    if (cartItems.length > 0) {
        cartItems.forEach(item => {
        htmlContent += `
          <tr data-id="${item.id}">
              <td data-th="Product">
                  <div class="row">
                      <div class="col-sm-3 hidden-xs"><img src="${item.image}" width="100" height="100" class="img-responsive"/></div>
                      <div class="col-sm-9">
                          <h4 class="nomargin">${item.title}</h4>
                      </div>
                  </div>
              </td>
              <td data-th="Price">$${item.price}</td>
              <td data-th="Quantity">
                <div class="text-center">${item.qty}</div>
              </td>
              <td data-th="Subtotal" class="text-center">$${item.price * item.qty}</td>
              <td class="actions" data-th="">
              </td>
          </tr>
        `;
      });
    }else{
        htmlContent += `
          <tr>
              <td data-th="Product">
                  <div class="row">
                      <div class="col-sm-12">
                          <h4 class="nomargin">No item in your cart</h4>
                      </div>
                  </div>
              </td>
          </tr>
        `;
    }

    $('#cart_summary').html(htmlContent);
    $('#cart_total').html(
        `<h3><strong>Total $${cartItems.reduce((init, item) => init + (parseInt(item.price) * parseInt(item.qty)), 0)}</strong></h3>`
    );
}

var stripe = Stripe('pk_test_51MJDv6ARcwgEa8pN8zfKbxPauRmBCufIHHNnYs0ocuEjX5JLdFu3Il0oKnQ0mchFtXIicXW8qobJlI9TSwW3oTdT00FiZOmNPl');
var elements = stripe.elements();
var style = {
  base: {
    color: "#32325d",
  }
};

var card = elements.create("card", { style });
card.mount("#card-element");

// Handle form submission
$('#payment-form').on('submit', function(event) {
    event.preventDefault();

    stripe.createToken(card).then(function(result) {
        if (result.error) {
            alert(result.error.message);
        } else {
            submitCart(result.token);
        }
    });
});

// Submit the form with the token to your server for processing

function submitCart(token) {
  var cartItems = JSON.parse(localStorage.getItem('cart')) || [];
  var total = cartItems.reduce((acc, item) => acc + (parseInt(item.price) * parseInt(item.qty)), 0);
    $.ajax({
        url: "{{ route('payment.process') }}",
        type: "post",
        data: {
            _token: "{{ csrf_token() }}", 
            stripeToken: token,
            total: total,
            cart: cartItems,
        }
      }).done(
        function (response) {
           if(response.success){
                 alert(response.message);
                 localStorage.removeItem('cart');
           }else{
                alert(response.message);
           }
      }
    );

    

}
</script>
@endsection