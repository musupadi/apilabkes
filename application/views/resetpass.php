<!DOCTYPE html>
<html><head>
	<meta charset="utf-8" name="viewport" content="width=500">
	<title>Validasi</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body style="background-color: #ecf0f1;">

<div class="container mt-5">
	<div class="row">
		<div class="col-md-3">
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-body">
                <?php foreach ($token as $data){

                ?>
					<center>
						<img src="https://portal.podomorouniversity.ac.id/assets/icon/logo_pu.png" class="img-fluid mb-3" style="width:70px">

						<form method="POST" action="<?php echo base_url('inputpublicapi/guestChangePassword')?>" enctype="multipart/form-data">
		                <input type="hidden" name="email" value="<?php echo $data['Email']?>">
                        <input type="hidden" name="token" value="<?php echo $data['Token']?>">
						<center>Masukkan Password Baru Anda</center><hr>
						
						<div class="form-group">
		                  <label for="">Password Baru</label>
		                  <input type="password" name="password_baru" class="form-control" aria-describedby="" required="">
		                </div>

		                <div class="form-group">
		                  <label for="">Konfirmasi Password</label>
		                  <input type="password" name="password_konfirmasi" class="form-control" aria-describedby="" required="">
		                </div>

						<button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Simpan</button>
						</form>
					</center>
                    
                    <?php }?>
				</div>
			</div>
		</div>
		<div class="col-md-3">
		</div>
	</div>
			
</div>



<div>
	
</div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body></html>