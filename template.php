<?php
	/**
	*
	* Template Name: TEMPLATE NAME
	*/
	$tag = 'TAG';
	$source = !empty( $_GET['_wmps'] ) ? htmlspecialchars( $_GET['_wmps'] ) : 'mummypages';

	$path_to_img = get_bloginfo( 'stylesheet_directory' ) . '/public/'. $tag .'/img/';
	$path_to_css = get_bloginfo( 'stylesheet_directory' ) . '/public/'. $tag .'/css/';
	$path_to_javascript = get_bloginfo( 'stylesheet_directory' ) . '/public/'. $tag .'/js/';


	if( $source == 'magicmum' ){
		$terms_url = '{{ magicmum_terms_url }}';
		$like_url = '{{ magicmum_like_url }}';
		$facebook_page = '{{ magicmum_facebook_page }}';
	}else{
		$terms_url = '{{ mummypages_terms_url }}';
		$like_url = '{{ mummypages_like_url }}';
		$facebook_page = '{{ mummypages_facebook_page }}';

		if( ( $tok = Helpers::token_exists() ) !== false ){
			// there is a token, user is logged in. attempt to retrieve his data
			$user_data = Helpers::get_user_data( $tok );
			$original_data = $user_data;
			if( $user_data !== false && $user_data != NULL ){
				$data_retrieved = true;
				$user_data = Helpers::build_user_data( $original_data );
			}else{
				$data_retrieved = false;
				$user_data = Helpers::build_user_data( array() );
			}
		}else{
			// attempt login, etc
			$data_retrieved = false;
			$user_data = Helpers::build_user_data( array() );
		}
	}

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script src="<?php echo $path_to_javascript ?>functions.js"></script>

        <script src="<?php echo $path_to_javascript ?>validate.js"></script>

        <link rel="stylesheet" href="<?php echo $path_to_css ?>normalize.css">
        <link rel="stylesheet" href="<?php echo $path_to_css ?>styles.css">

        <?php wp_head() ?>
    </head>
    <body>

        <!--[if lte IE 8]>
        <div class="browsehappycontain">
			<p class="browsehappy message-box">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        </div>
        <![endif]-->



        <script>
            // form stuff
            var $form = jQuery('#entry');
            var $submit = $form.find('.submit');
            var $preloader = $('#preloader');

            function ajax_post(){
                $preloader.hide();
            }

            function ajax_post_failed(message){
                alert(message);
            }

            $submit.on('click', function(evt){
                $form.submit();
                evt.preventDefault();
            });

            $form.validate({
                onSubmitErrorCallback: function(errors){
                    ajax_post_failed('There are errors in the form.');
                    for( var elementName in errors ){
                        var trigger;
                        trigger = $form.find('[name="' + elementName + '"]');
                        trigger.addClass('onError');
                        trigger.one('focus click', function(){
                            $(this).removeClass('onError');
                        })
                    }
                },
                onSubmitSuccessCallback: function(){
                    $preloader.show();

                    $.ajax({
                        url: '/wp-admin/admin-ajax.php',
                        type: "POST",
                        data: {
                            action: 'processAjaxForm',
                            formdata: $form.serializeArray()
                        },
                        dataType: "json",
                        accepts: "json"
                    }).done(function(response){
                        if( response.result ){
	                         // SUCCESS ACTIONS
                        }else{
                            if( response.code == 25 ){
                                ajax_post_failed('Email already used for entry.');
                            }else{
                                ajax_post_failed('There are errors in the form.');
                            }
                        }
                    }).fail(function(response){
                        ajax_post_failed('An unexpected error has ocurred');
                    }).always(function(){
                        ajax_post();
                    });
                }
            });


            var $countySelect = $('#county');
            var $neighbourhoodSelect = $('#neighbourhood');
            $countySelect.change(function() {
                var current_county = $countySelect.val();

                $neighbourhoodSelect.html('').append('<option value="" selected="selected">Select county first</option>');
                if(current_county != ''){
                    $neighbourhoodSelect.html('');
                    update_neighbourhoods(current_county, '');
                }
            });

            if( $countySelect.val() != '' ){
                update_neighbourhoods($countySelect.val(), "<?php echo $user_data['neighbourhood'] ?>");
            }else{
                $neighbourhoodSelect.html('').append('<option value="" selected="selected">Select county first</option>');
            }

            function update_neighbourhoods(county, neighbourhood){
                if( neighbourhood == '' ){
                    $neighbourhoodSelect.append('<option value="" selected="selected">Please select</option>');
                }else{
                    $neighbourhoodSelect.append('<option value="">Please select</option>');
                }

                <?php he_neighbourhoods_array_js(); ?>

                var values = neighbourhoods[county];
                $.each(values, function( key, item ){
                    if( item == neighbourhood ){
                        $neighbourhoodSelect.append( '<option value="' + item + '" selected="selected">' + item + '</option>' );
                    }else{
                        $neighbourhoodSelect.append( '<option value="' + item + '">' + item + '</option>' );
                    }
                });
            }
        });
        </script>

        <?php wp_footer() ?>
    </body>
</html>
