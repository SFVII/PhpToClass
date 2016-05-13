<?php 
/**
 * @Author: brice@nippon.wtf
 * @Date:   2016-05-13 13:19:35
 * @Last Modified by:   Ohoh
 * @Last Modified time: 2016-05-13 16:05:29
 */
?>

<!DOCTYPE html>
<html>
<head>
	<title>Model Generator Mysql Databe to PhpClass ::</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<style type="text/css">
		html, body, section{
			width: 100%;
			height: 100%;
			background:#2980b9;
		}
		.container{
			background:white;
		}
		#log{
			display:none;
			margin-top: 2em;
			background : rgba(0,0,0,0.7);
			color:white;
		}
	</style>
</head>
<body>
	<section class="full-width">
		
		<article class="container" style="height:100%">
		<aside class="row">
			<span class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
				<img src="https://octodex.github.com/images/inspectocat.jpg" width="150px">
			</span>
			<span class="col-xs-6 col-sm-8 col-md-8 col-lg-8">
				<h1 style="text-align: justify">Create class <br>hasn't been soooo <br>easy !!!</h1>
			</span>
		</aside>
		<hr>
		<aside class="col-xs-12" styl>
			<form id="db2class" method="post">
				<input type="text" name="db" placeholder="Database Name" class="form-control" required><br>
				<input type="text" name="table" placeholder="Table Name" class="form-control" required><br>
				<input type="text" name="name" placeholder="class Name" class="form-control" required><br>
				<input type="text" name="extends" placeholder="Extends Name" class="form-control"><br>
				<input type="text" name="dest" placeholder="Directory (if empty current directory)" class="form-control"><br>
				<button type="submit" class="btn btn-primary pull-right">Create</button>
			</form>
		</aside>

		<aside class="col-xs-12" id="log">

		</aside>
		</article>
	</section>
</body>
<script src="https://code.jquery.com/jquery-2.2.3.js"   integrity="sha256-laXWtGydpwqJ8JA+X9x2miwmaiKhn8tVmOVEigRNtP4="   crossorigin="anonymous"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#db2class').on('submit', function(e){
			e.preventDefault();
			$('#log').empty(200);
			$.ajax({
				type: 'post',
				url: '?submit=true',
				data: $('#db2class').serialize(),
				success: function (msg){
					$('#log').append(msg);
					$('#log').show(500);
				}
			})
		});
	});
</script>
</html>