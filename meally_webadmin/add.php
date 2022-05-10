<?php
session_start();
?>

<div class="cotainer">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"></div>

                 
            </div>
            <div class="card-body">
                <form action="code_add.php" method="POST">

                    <div class="form-group mb-3">
                        <label for="">Store Address</label>
                        <input type="text" name="storeaddress" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Store BIN</label>
                        <input type="text" name="storebin" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Store Name</label>
                        <input type="text" name="storename" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Store Owner</label>
                        <input type="text" name="storeowner" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <button type="submit" name="save" class="btn btn-primary"> Save </button>
                    </div>
             </form>
            </div>  
        </div>
    </div>
</div>