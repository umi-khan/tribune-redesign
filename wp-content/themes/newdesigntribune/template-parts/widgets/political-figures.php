<?php
$provices = array("Islamabad", "Sindh", "Balochistan", "Punjab", "KP", "FATA");
$parties  = array( "ppp", "pmln", "pti",  "mqm", "pmlq", "anp", "ji", "jui-f" );

$parties_position = get_option( 'parties_position', array() );
if( is_serialized($parties_position) ) $parties_position = unserialize ($parties_position);
?>

<div class="political-figures widget">
	<h4>NATIONAL ASSEMBLY RESULTS</h4>
   <div class="carousel-pagination"></div>
			
   <div class="content clearfix">
      <div class="row-head column">
         <div class="column-cell column-head">Seats</div>
         <?php 
         foreach ($provices as $provice )  : ?>
         <div class="column-cell"><?php echo $provice;?></div>
         <?php         
         endforeach; ?> 
         <div class="column-cell column-foot">Total</div>
      </div>
      <div class="rows">
         <div class="carousel clear">
            <div class="items clearfix">
            <?php 
            for( $i = 0, $j = count( $parties ); $i < $j; $i++ ):   
               $args['is_first'] = FALSE;
               $args['is_last']  = FALSE; 
               $class = '';
               if( $i%4 == 0 )    {$class = ' first';$args['is_first'] = TRUE;}
               elseif($i%4 == 3 ) {$class = ' last';$args['is_last'] = TRUE;}      ?>
               <div class="item column<?php echo $class;?>">
                  <div class="column-head column-cell"><?php echo $parties[$i];?></div>
                 <?php
                  $total = 0;
                  foreach ($provices as $provice )  : ?>
                     <div class="column-cell">
                        <?php if( isset( $parties_position[$provice][$parties[$i]] ) && $parties_position[$provice][$parties[$i]] > 0 ){ 
                           echo $parties_position[$provice][$parties[$i]];
                           $total += (int)$parties_position[$provice][$parties[$i]]; } else { echo '-';}?>
                     </div>
                  <?php         
                  endforeach; ?>
                  <div class="column-cell column-foot "><?php echo $total;?></div>
               </div>   
            <?php 
            endfor;?>
            </div>
         </div>
       </div>  
   </div>   
</div>   
