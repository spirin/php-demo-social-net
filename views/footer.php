		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
		<script src="/js/main.js"></script>
		<script src="/js/datepicker/bootstrap-datepicker.min.js"></script>
		<script src="/js/datepicker/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
	
		<?php foreach ($this->scripts as $script) : ?>
			<?php echo $script; ?>
		<?php endforeach ?>
	</body>
</html>