
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Select Panel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    margin:0;
    min-height:100vh;
    background:linear-gradient(135deg,#004392,#0d6efd);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}

.panel-wrapper{
    width:100%;
    max-width:900px;
}

.panel-container{
    position:relative;
    background:#fff;
    border-radius:20px;
    padding:40px;
    box-shadow:0 20px 60px rgba(0,0,0,0.2);
}

.back-btn{
    position:absolute;
    top:20px;
    left:20px;
    width:42px;
    height:42px;
    border-radius:50%;
    background:#f1f5ff;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:0.3s;
}

.back-btn:hover{
    background:#dbe7ff;
}

.panel-title{
    text-align:center;
    font-weight:700;
    font-size:32px;
    color:#004392;
    margin-bottom:5px;
}

.panel-subtitle{
    text-align:center;
    color:#777;
    margin-bottom:35px;
}

.panel-card{
    position:relative;
    background:#f7f9fd;
    border:2px solid transparent;
    border-radius:18px;
    padding:35px 20px;
    text-align:center;
    cursor:pointer;
    transition:0.3s ease;
    height:100%;
}

.panel-card:hover{
    transform:translateY(-4px);
}

.panel-card.active{
    border-color:#004392;
    background:#edf4ff;
    box-shadow:0 8px 25px rgba(0,67,146,0.15);
}

.panel-card.disabled{
    opacity:0.6;
    cursor:not-allowed;
}

.panel-card input{
    position:absolute;
    top:15px;
    right:15px;
    transform:scale(1.3);
}

.icon{
    font-size:45px;
    margin-bottom:10px;
}

.title{
    font-size:20px;
    font-weight:700;
    color:#111;
}

.desc{
    font-size:14px;
    color:#666;
    margin-top:5px;
}

.continue-btn{
    margin-top:35px;
    background:#004392;
    color:#fff;
    border:none;
    padding:14px 35px;
    border-radius:50px;
    font-weight:600;
    transition:0.3s;
}

.continue-btn:hover{
    background:#003170;
}

.error-box{
    display:none;
    margin-top:20px;
}

</style>

</head>

<body>

<div class="panel-wrapper">

<div class="panel-container">

<div class="back-btn" onclick="history.back()">
    <i class="bi bi-arrow-left"></i>
</div>

<h2 class="panel-title">
    Choose Your Workspace
</h2>

<p class="panel-subtitle">
    You can select multiple modules
</p>

<form method="POST"
      action="{{ route('seller.switch.panel.multi') }}"
      id="panelForm">

    @csrf

    <div class="row g-4">

        <!-- VSHOP -->
        <div class="col-md-6">

            <div class="panel-card selectable-card"
                 onclick="toggleCard(this)">

                <input type="checkbox"
                       name="panels[]"
                       value="product">

                <div class="icon">🛒</div>

                <div class="title">
                    V-Shop
                </div>

                <div class="desc">
                    Manage products & orders
                </div>

            </div>

        </div>

        <!-- VTRAVEL -->
        <div class="col-md-6">

            <div class="panel-card selectable-card"
                 onclick="toggleCard(this)">

                <input type="checkbox"
                       name="panels[]"
                       value="travel">

                <div class="icon">✈️</div>

                <div class="title">
                    V-Travel
                </div>

                <div class="desc">
                    Travel services
                </div>

            </div>

        </div>

        <!-- VCAMPUS -->
        <div class="col-md-6">

            <div class="panel-card selectable-card"
                 onclick="toggleCard(this)">

                <input type="checkbox"
                       name="panels[]"
                       value="vcampus">

                <div class="icon">🎓</div>

                <div class="title">
                    V-Campus
                </div>

                <div class="desc">
                    Courses & Campus Management
                </div>

            </div>

        </div>

        <!-- VEDGE -->
        <div class="col-md-6">

            <div class="panel-card disabled">

                <div class="icon">⚡</div>

                <div class="title">
                    V-Edge
                </div>

                <div class="desc">
                    Coming Soon
                </div>

            </div>

        </div>

    </div>

    <div class="text-center">

        <div class="alert alert-danger error-box"
             id="panelError">
            Please select at least one workspace
        </div>

        <button type="submit"
                class="continue-btn">
            Continue
        </button>

    </div>

</form>

</div>

</div>

<script>

function toggleCard(el){

    if(el.classList.contains('disabled')){
        return;
    }

    let checkbox = el.querySelector('input');

    checkbox.checked = !checkbox.checked;

    if(checkbox.checked){
        el.classList.add('active');
    }else{
        el.classList.remove('active');
    }

    $('#panelError').hide();
}


$('#panelForm').submit(function(e){

    let checked = $('input[name="panels[]"]:checked').length;

    if(checked === 0){

        e.preventDefault();

        $('#panelError').fadeIn();

        return false;
    }
});

</script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</body>
</html>

