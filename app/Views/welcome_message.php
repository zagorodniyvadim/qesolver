<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Welcome to CodeIgniter 4!</title>
	<meta name="description" content="The small framework with powerful features">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="/favicon.ico"/>

	<!-- STYLES -->

	<style>
        form {
            width: 100%;
            text-align: center;
            margin: 100px 0 0
        }
        form input {
            width: 30px ;
        }
	</style>
</head>
<body>

<form id="equation_form">
    <input type="text" name="a" id="">x&sup2; +
    <input type="text" name="b" id="">x +
    <input type="text" name="c" id=""> = 0
</form>

<div class="result_block" id="result_error">
    <p></p>
</div>
<div class="result_block" id="result_two_solutions">
    <p></p>
</div>
<div class="result_block" id="result_single_solution">
    <p></p>
</div>
<div class="result_block" id="result_no_solution">
    <p></p>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-sha1/0.6.0/sha1.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js"></script>-->
<script>
    $(function() {
        const isInteger = function(value) {
            return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
        }
        const showBlock = function(block, text) {
            $('.result_block').hide();
            const block_obj = $('#' + block);
            block_obj.show();
            $('p', block_obj).html(text);
        }
        $('form input').keyup(function() {
            var form_data = {};
            $("form").find(":input").each(function() {
                form_data[this.name] = $(this).val();
            });

            $('.result_block').hide();

            if(!isInteger(form_data.a)) {
                showBlock('result_error', 'The a field must contain an integer');
                return;
            }
            if(form_data.a == 0) {
                showBlock('result_error', 'The a field must not be 0');
                return;
            }
            if(!isInteger(form_data.b)) {
                showBlock('result_error', 'The b field must contain an integer');
                return;
            }
            if(!isInteger(form_data.c)) {
                showBlock('result_error', 'The c field must contain an integer');
                return;
            }

            form_data['token'] = sha1(form_data.a + form_data.b + form_data.c);
            $.post(
                '/api/equation',
                form_data,
                function (data) {
                    if(data.status == -1) {
                        showBlock('result_error', data.message);
                    }
                    if(data.status == 1) {
                        showBlock('result_two_solutions', 'x1 = ' + data.x1 + '<br>x2 = ' + data.x2 );
                    }
                    if(data.status == 2) {
                        showBlock('result_single_solution', 'x1 = ' + data.x1);
                    }
                    if(data.status == 3) {
                        showBlock('result_no_solution', data.message);
                    }
                },
                "json"
            ).fail(function() {
                showBlock('result_error', 'Request error');
            });
        });
    });
</script>
</body>
</html>
