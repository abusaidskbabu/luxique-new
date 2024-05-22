<!DOCTYPE html>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&family=Poppins&display=swap" rel="stylesheet">

<style>

.dashboard-section{
    width: max-content;
    padding: 6px 15px 6px 15px;   
}

.barcode-container {
    margin-top: 12px
}

.barcode-label-container{
    text-align: center;
}
.barcode{
font-family: 'Libre Barcode 39', cursive;
}

.dashboard-section{
    width: max-content;
    padding: 6px 15px 6px 15px;   
}

.barcode-container {
    margin-top: 12px
}

.barcode-label-container{
    text-align: center;
}
.barcode{
font-family: 'Libre Barcode 39', cursive;
font-size: 40px;
}

</style>
<body onload="window.print()">
    <div class="dashboard-section" >
      <div class="barcode-label-container">
        <div id="barcode-label" class="barcode-label">
          <div class="label-item-barcode label-field">
            <div class="barcode-container">
                <div class="barcode">
                   <img src="data:image/png;base64, {{DNS1D::getBarcodePNG(htmlspecialchars($product->barcode), 'C39',5,10) }}" alt=""  width="200px" height="50px">
                 </div>
            </div>
            
            <div class="item-code">
                  {{ $product->barcode  }}
            </div>
            <div class="label-item-price label-field">
                BDT {{ number_format($product->price)  }}
            </div>
            
    
          </div>
        </div>
      </div>
    </div>
</div>
</html>