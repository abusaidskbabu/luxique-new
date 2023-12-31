<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

  <title>{{'LuxiQue'. ($meta['title'] ? ' - '.$meta['title'] : '') }}</title>
	<meta charset="utf-8">
  <meta name="theme-color" content="#EB8524">
  <meta name="msapplication-navbutton-color" content="#EB8524">
  <meta name="apple-mobile-web-app-status-bar-style" content="#EB8524">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="google-signin-client_id" content="397239095845-llm31ean6e5v33s3r8lhucahvk1amnko.apps.googleusercontent.com">
	<meta property="og:url" content="{{ url()->current()}}" />
	<meta property="og:type" content="{{ $meta['type'] ?? 'article'}}" />
	<meta property="og:title" content="{{'LuxiQue - '.$meta['title'] ?? ''}}" />
  <meta property="og:keyword" content="{{$meta['keyword'] ?? ''}}" />
	<meta property="og:description" content="{{$meta['description'] ?? ''}}" />
	<meta property="og:image" content="{{$meta['image'] ?? ''}}" />
  <link rel="shortcut icon" href="{{ env('APP_API_URL').'/'.\DB::table('settings')->where('key','favicon')->first()->value ?? '/assets/images/favicon.png' }}" />
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  <link rel="stylesheet" type="text/css" href="/assets/slick/slick.css"/>
  <link rel="stylesheet" href="/assets/css/bootstrap.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link type="text/css" href="css/themename/jquery-ui-1.7.1.custom.css" rel="Stylesheet" />   
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/zoom-images.css">
  <link rel="stylesheet" href="/assets/css/shimmer.css">
  <link rel="stylesheet" type="text/css" href="/assets/css/custom.css"/>
  <link rel="stylesheet" href="/assets/css/responsive.css">
  <script src="/assets/js/jquery.js"></script> 
  <script defer src="{{ mix('js/vue/entry-client.js') }}"></script>
  <script src="/assets/js/popper.js"></script>
  <script src="/assets/js/parts/product.js"></script>
  <script src="/assets/js/parts/product_grid.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">

</head>
<body>

  @php $headerScript = \DB::table('settings')->where('key','website_header_script')->first();@endphp
  @if($headerScript)
   {!! $headerScript->value !!}
  @endif
  
  <script>
    if(!localStorage.getItem('session_key')){
      localStorage.setItem('session_key',"{{$session_key ?? '' }}");
    }
  </script>
  

 {!! ssr('js/vue/entry-server.js')
  ->context('meta', ($meta['ssr'] ?? ''))
  ->fallback('<div id="app"></div>')
  ->render() !!}
  <!-- Search Engine Optimization, Automatically Generated By NodeJs V8 Engine <div id="SEO">{!! $meta['ssr'] !!}</div> -->

  <script src="/assets/js/myzoom-image.js"></script>
  <script src="/assets/js/bootstrap.js"></script>

  <div class="modal_offer">
    <div class="modal_offer_content">
      <div class="modal_offer_header">
        <span id="close-modal">&times;</span>
      </div>
      <div class="modal_offer_body">
        <img style="width:100%" src="/images/sale-promo-2.png" alt="promo">
      </div>
    </div>
  </div>

  <script>
    const modal = document.querySelector(".modal_offer");
    const closeModalBtn = document.querySelector("#close-modal");
    closeModalBtn.addEventListener("click", function () {
      modal.style.display = "none";
    });

    $(document).ready(function(){
      const modal = document.querySelector(".modal_offer");
    })
  </script>


<script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>

<script>
    var firebaseConfig = {
		apiKey: "AIzaSyD-1qTy-COPv7IT4u_X8uCwoSPV4mOUDJw",
		authDomain: "khola-bazaar.firebaseapp.com",
		projectId: "khola-bazaar",
		storageBucket: "khola-bazaar.appspot.com",
		messagingSenderId: "108629861013",
		appId: "1:108629861013:web:236f1d80012c4c260a319c",
		measurementId: "G-CV7FC7EPLZ"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
            messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function(token) {
                //console.log(token);
                $.ajax({
                    url: '{{ route("save-token") }}',
                    type: 'POST',
                    data: {
                        token: token,
                        user_id: localStorage.getItem('userID')
                    },
                    dataType: 'JSON',
                    success: function (response) {
                       // alert('Token saved successfully.');
                    },
                    error: function (err) {
                        console.log('User Chat Token Error'+ err);
                    },
                });

            }).catch(function (err) {
                console.log('User Chat Token Error'+ err);
            });
     }

    messaging.onMessage(function(payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    });

    jQuery(document).ready(function(){
      initFirebaseMessagingRegistration();
    });

</script>




@php $footerScript = \DB::table('settings')->where('key','website_footer_script')->first();@endphp
@if($footerScript)
 {!! $footerScript->value !!}
@endif

</body>
</html>
  

