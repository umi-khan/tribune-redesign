<?php
/**
 * Template Name: Contact Page
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); 

$server_name = ucwords($_SERVER['SERVER_NAME']);

if( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' )
	$map_api_key = 'ABQIAAAAlzlMYkyeWaUKdgW4QGpCjxSHfSzwT3ZTXc4P6CRllrIUZW7IfxS4m1A0lFISZpzLqqCYzWP44E4cQw';
else
	$map_api_key = 'ABQIAAAAlzlMYkyeWaUKdgW4QGpCjxTrBZQjr-CfpTy2M0zsAY1TmpL4hhRQKtj-zVYImz61a55kpaD2mnIzeA';

//wp_register_script('google-maps', 'http://maps.google.com/maps?file=api&v=1&key='.$map_api_key  );

add_action( 'wp_print_footer_scripts', 'print_scripts' );
function print_scripts()
{
	wp_print_scripts('google-maps');
}

?>
<div class="col-lg-8">
<div class="span-24 page-contact-us">
    <div class="primary span-16">
			<h2>Contact Us</h2>
			<h3>The Express Tribune's Headquarters</h3>
			<div id="address-container" class="clearfix" >
				<!--- Place view map link here -->
				<div id="map"><img alt="" src="http://maps.google.com/maps/api/staticmap?&zoom=14&size=605x308&maptype=roadmap&markers=color:red|24.831660,67.079477&sensor=false"/></div>
			</div>

			<h3>How to contact The Express Tribune</h3>			
			<h6>Please address any correspondence to:</h6>
			<p>The Express Tribune Office <br/>
			5 Expressway, Off Korangi road<br/>
			Karachi, 75500<br/>
			Pakistan</p>

			<p>The switchboard number for The Express Tribune is: <span class="contact-num">111-397-737</span></p>
			<p>or dialing from overseas <span class="contact-num">+92-21-111-397-737</span> and <span class="contact-num">+92-21-358-000-51</span> to <span class="contact-num">58</span></p>
			<p><strong>Fax Number:</strong> 35800050 </p><br/>

			<p>Islamabad Bureau Office<br/>
			15-1 & T Center<br/>
			Khayabane Suharwardi, Abpara Road<br/>
			Islamabad<br/>
			Phone: 051 2879121-8</p><br/>

			<p>Lahore Bureau Office<br/>
			Plot #229-A, Main FerozPur Road<br/>
			Lahore<br/>
			Phone: 042 5847522</p>
			
			<h6>Website user help at tribune.com.pk</h6>
			<p>For any problems using the website and its links, or general questions and feedback about tribune.com.pk, email: <a target="_blank" href="mailto:web@tribune.com.pk">web@tribune.com.pk</a></p>
			
			<h3>To contact editorial departments/desks</h3>
			<p>See the list below - please take care to send queries to the correct destination as there is no guarantee that messages will be forwarded.
			</p>

			<h3>The Express Tribune: Editorial departments and desks</h3>
			<dl class="clearfix">
				<dt class="col-lg-4">National desk </dt><dd><a target="_blank" href="mailto:national@tribune.com.pk">national@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Karachi desk </dt><dd><a target="_blank" href="mailto:karachi@tribune.com.pk">karachi@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Lahore desk </dt><dd><a target="_blank" href="mailto:lahore@tribune.com.pk">lahore@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Islamabad desk </dt><dd><a target="_blank" href="mailto:islamabad@tribune.com.pk">islamabad@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Peshawar desk </dt><dd><a target="_blank" href="mailto:peshawar@tribune.com.pk">peshawar@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Opinion &amp; Editorial desk </dt><dd><a target="_blank" href="mailto:opinions@tribune.com.pk">opinions@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Business desk </dt><dd><a target="_blank" href="mailto:biz@tribune.com.pk">biz@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Sports desk </dt><dd><a target="_blank" href="mailto:sports@tribune.com.pk">sports@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Life &amp; Style desk </dt><dd><a target="_blank" href="mailto:style@tribune.com.pk">style@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Magazine desk </dt><dd><a target="_blank" href="mailto:magazine@tribune.com.pk">magazine@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Web desk </dt><dd><a target="_blank" href="mailto:web@tribune.com.pk">web@tribune.com.pk</a></dd>
				<dt class="col-lg-4">Blog desk </dt><dd><a target="_blank" href="mailto:blog@tribune.com.pk">blog@tribune.com.pk</a></dd>
			</dl>

			<h6>To contact any member of staff</h6>
			<p>Individual staff can be contacted using this email format: <a href="javascript:void(0);">firstname.lastname@tribune.com.pk</a></p>

			<h6>Letters to the editor</h6>			
			<p>Letters for publication should be sent to <a target="_blank" href="mailto:letters@tribune.com.pk">letters@tribune.com.pk</a></p>
			
			<h6>Corrections and clarifications of content</h6>
			<p>
				It is the policy of The Express Tribune to correct factual/editorial errors and handle editorial complaints as soon as possible. 
			</p>

			<p>Contact: <a target="_blank" href="mailto:editorial@tribune.com.pk">editorial@tribune.com.pk</a></p>			
			<p>
				[* Please note that queries about subscriptions, vouchers, reader offers, advertising, marketing and other non-editorial areas are not handled by the above. These should be addressed to the relevant departments (see above). Story suggestions should also be sent to the relevant departments.]			</p>
<h6>Comment Moderation</h6>
			<p>For more information about comment moderation, <a target="_blank" href="<?php echo home_url('comments-policy');?>">click here</a> or contact <a target="_blank" href="mailto:web@tribune.com.pk">web@tribune.com.pk</a></p>

			<h3>Advertisers, sponsorship and e-commerce</h3>
			<p>To find out more about all advertising opportunities in The Express Tribune</p>
			<p>
			Email: <a target="_blank" href="mailto:advertise@tribune.com.pk">advertise@tribune.com.pk</a>
			</p>

			<h3>Freelance contributions</h3>
			<p>Story pitches should be sent to relevant editorial department, as listed above.</p>

			<h3>Employment and work experience</h3>
			<p>If you are interested in working for The Express Tribune or the Express Media Group please <a target="_blank" href="<?php echo home_url('careers');?>">click here</a>.</p>

			<p>You can also email <a target="_blank" href="mailto:workforus@tribune.com.pk">workforus@tribune.com.pk</a> or call 35800051-58 /35804227</p>


			<h3>Circulation/Subscriptions</h3>
			<p>If your query concerns a subscription please <a target="_blank" href="<?php echo home_url('subscribe');?>">click here</a>.</p>

			<p>You can also contact <a target="_blank" href="mailto:subscription@tribune.com.pk">subscription@tribune.com.pk</a> or call:</p>
			<ul>
				<li>Karachi: (021) 353 188 66</li>
				<!-- li>Lahore: (042) 358 7094</li>
				<li>Islamabad: (051) 260 3253</li -->
			</ul>

			<h3>Content distribution and syndication</h3>
			<p>If you wish to re-publish The Express Tribune articles or photography, please email <a target="_blank" href="mailto:editorial@tribune.com.pk">editorial@tribune.com.pk</a> or call our overseas number (above).</p>						
	</div>
</div>
</div>
<div class="col-lg-4">
<?php dynamic_sidebar('sidebar-5'); ?>

</div>
</div>

<?php get_footer(); ?>
