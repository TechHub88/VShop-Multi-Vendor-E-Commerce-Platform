@extends('frontend.layout')

<style>
    .bedcum-sec {
        /*background: #000;*/
        background-image: url(https://kidzz.in/mafatlal/public/uploads/about/1762590153.jpg);
        background-position: 50% 50%;
        background-repeat: no-repeat;
        background-size: cover;
        color: #fff;
        padding: 80px 0 !important;
    }
    .bedcum-inner-sec {
        text-align: center;
    }
    .bedcum-inner-sec ul {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .bedcum-inner-sec ul li a {
        font-size: 16px;
        font-weight: 500;
        color: #ed3237;
    }
    .bedcum-inner-sec ul li {
        font-size: 16px;
        font-weight: 500;
    }
    
    .contact-top-sec {
        width: 100%;
        padding: 60px 0;
    }
    .contact-top-box {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }
    .contact-icon {
        width: 70px;
        height: 70px;
        background: #ed3237;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #fff;
    }
    .contact-info {
        width: 80%;
        padding-left: 15px;
    }
    .contact-info h4 {
        font-size: 20px;
    }
    .contact-info p {
        font-size: 17px;
        margin: 0;
    }
    .contact-info p a {
        font-size: 17px;
        color: #000;
    }
    .contact-top-right {
        width: 100%;
        height: 100%;
        border: 1px solid rgba(34, 31, 32, .1);
        border-radius: 22px;
        box-shadow: 0 0 14.604644775390625px #221f2014;
        position: relative;
        background: #fff;
        padding: 25px;
    }
    .contact-top-right h2 {
        font-size: 22px;
        margin-bottom: 20px;
    }
    .form-input-box {
        margin-bottom: 24px;
    }
    .form-input-box input {
        background: transparent;
        border: 1px solid #c1c1c1;
        height: 45px;
        padding: 8px 14px;
        color: #000;
        width: 100%;
        outline: none;
        font-size: 15px;
        border-radius: 5px;
    }
    .form-input-box textarea {
        background: transparent;
        border: 1px solid #c1c1c1;
        height: 80px;
        padding: 8px 14px;
        color: #000;
        width: 100%;
        outline: none;
        font-size: 15px;
        border-radius: 5px;
        resize: none;
    }
    .form-btn-box .send-btn {
        background: #ed3237;
        border: 0;
        color: #fff;
        font-size: 20px;
        font-weight: 500;
        padding: 7px 22px;
        border-radius: 5px;
    }
</style>

@section('content')

<section class="bedcum-sec">
    <div class="container">
        <div class="bedcum-inner-sec">
            <h1>Contact Us</h1>
            <ul>
                <li>
                    <a href="#">Home</a>
                </li>
                <li>
                    /
                </li>
                <li>Contact Us</li>
            </ul>
        </div>
    </div>
</section>

<section class="contact-top-sec">
    <div class="container">
        <div class="contact-top-inner">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="contact-top-left">
                        <div class="contact-top-box">
                            <div class="contact-icon">
                                <i class="fa fa-map-marker"></i>
                            </div>
                            <div class="contact-info">
                                <h4>Office Location</h4>
                                <p>Mafatlal House,4th Floor, H.T.Parekh Marg, Backbay Reclamation, Mumbai-400 020</p>
                            </div>
                        </div>
                        <div class="contact-top-box">
                            <div class="contact-icon">
                               <i class="fa fa-phone"></i>
                            </div>
                            <div class="contact-info">
                                <h4>Calling Support</h4>
                                <p><a href="tell:+91-22-6617 3636">+91-22-6617 3636</a></p>
                            </div>
                        </div>
                        <div class="contact-top-box">
                            <div class="contact-icon">
                               <i class="fa fa-globe"></i>
                            </div>
                            <div class="contact-info">
                                <h4>Email Information</h4>
                                <p><a href="mailto:contact@arvindmafatlalgroup.com">contact@arvindmafatlalgroup.com</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-top-right">
                        <h2>Get In Touch</h2>
                        <div class="form-box">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-input-box">
                                        <input type="text" placeholder="Enter name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-input-box">
                                        <input type="email" placeholder="Enter email">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-input-box">
                                        <input type="tel" placeholder="Enter phone">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-input-box">
                                        <input type="text" placeholder="Enter subject">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-input-box">
                                        <textarea placeholder="Your message"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-btn-box">
                                        <button class="send-btn">SEND</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="map-sec">
    <div class="map-inner">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3684.1670956539137!2d88.43127107428712!3d22.57285293296305!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0275bc746c4705%3A0x7fd27804e31a0f94!2sWebMantra%20Technology%20Services%20Pvt%20Ltd.!5e0!3m2!1sen!2sin!4v1769768178417!5m2!1sen!2sin" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

@endsection



