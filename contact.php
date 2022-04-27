<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');?>
<div class="contact">
	<ul><lh class="blue"><?php echo DLC_FULL?></lh><li>
	Unit 2B, ABD Building, 64 Kamuning Road,<br />Quezon City, Philippines<br />
	</li></ul>
	<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d965.1195911768874!2d121.0394224498065!3d14.628765840732795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b6142583589f%3A0x81f836a84ea81be4!2sDLC+Philippines+Shareconomy!5e0!3m2!1sen!2sph!4v1552206896495" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>

	<ul><lh>Office Hours</lh><li>
	<span class="s3">Monday</span>1pm - 8pm<br />
	<span class="s3">Tue-Sat</span>9am - 8pm<br /><br />
	<span class="s3">Holidays</span>please contact the office to confirm schedule<br />
	</li></ul>

	<ul><lh>Hotlines</lh><li>
	<span class="s3">Landline:</span>+63 2 7729 3929<br />
	<span class="s3">Globe:</span>+63 915 170 7388<br />
	<span class="s3">Smart:</span>+63 947 692 8060<br />
	</li></ul>

	<ul><lh>E-mail</lh><li>
	<span class="s3">Concerns:</span><a href="mailto:distriservice@dlcph.com">distriservice@dlcph.com</a><br />
	</li></ul>

	<ul><lh>Facebook</lh><li>
	<span class="s3">Page:</span><a href="https://www.facebook.com/dlcphilippines">DLC Philippines</a><br />
	<span class="s3">Messenger:</span><a href="https://m.me/dlcphilippines">DLC Philippines</a><br />
	</li></ul>
	<?php //if(isset($addGOS)&&$addGOS) loadGOS();?>
</div>

<?php
function loadGOS(){
	$arr=array(
		array(
			'branch'=>'33001',
			'area'=>'Bacolod',
			'address'=>'L26 Don Fernando St., Villa Angela Subd., Brgy. Villamonte, Bacolod City',
			'oic'=>'Lordgeline Jones',
			'contact'=>'+63 908 8877982'
		),array(
			'branch'=>'44001',
			'area'=>'Marilao, Bulacan',
			'address'=>'Agoho St., Town and Country, North Marilao, Bulacan',
			'oic'=>'Sheryll Mendoza',
			'contact'=>'+63 932 5637323'
		),array(
			'branch'=>'46100',
			'area'=>'Dasmariñas, Cavite',
			'address'=>'Rm. 18 2/F Navjar Bldg., P. Campos Ave., Bayan, Dasmariñas, Cavite',
			'oic'=>'Ricky Dacillo',
			'contact'=>'+63 923 7014410'
		),array(
			'branch'=>'74002',
			'area'=>'Holy Ghost Ext, Baguio',
			'address'=>'215 Holy Ghost Ext, Baguio City',
			'oic'=>'Mercedes Busacay',
			'contact'=>'+63 906 7933219'
		),array(
			'branch'=>'78001',
			'area'=>'Diffun, Quirino',
			'address'=>'Andres Bonifacio, Diffun, Quirino',
			'oic'=>'Juliet Dacanay',
			'contact'=>'+63 906 3269553'
		),array(
			'branch'=>'83001',
			'area'=>'Gen San',
			'address'=>'Rm. 6 2/F, JMP 1 South Osmeña St., Gen. Santos City',
			'oic'=>'Fe Almasa',
			'contact'=>'+63 917 7631856'
		)
	);

	$x='<ul><lh>GOS</lh>';
	foreach($arr as $key=>$val){
		$x.='<li>';
		foreach($val as $i=>$v){
			$x.='<span class="s3">'.ucwords($i).':</span> '.utf8_encode($v).'<br />';
		}$x.='</li>';
	}$x.='</ul>';
	echo $x;
}
?>
