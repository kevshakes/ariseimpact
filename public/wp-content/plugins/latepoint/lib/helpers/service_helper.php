<?php 

class OsServiceHelper {

	public static function get_max_capacity_by_service_id($service_id){
		$service = new OsServiceModel($service_id);
		return self::get_max_capacity($service);
	}

	public static function get_min_capacity_by_service_id($service_id){
		$service = new OsServiceModel($service_id);
		return self::get_min_capacity($service);
	}


	public static function get_max_capacity($service){
		if($service && !empty($service->capacity_max)){
			return $service->capacity_max;
		}
		return 1;
	}

	public static function get_min_capacity($service){
		if($service && !empty($service->capacity_min)){
			return $service->capacity_min;
		}
		return 1;
	}

	public static function get_percent_of_capacity_booked($service, $total_attendies){
		$total_attendies = empty($total_attendies) ? 1 : $total_attendies;
		$max_capacity = self::get_max_capacity($service);
		return min(100, round($total_attendies / $max_capacity * 100));
	}

	public static function get_default_colors(){
		return array('#2752E4', '#C066F1', '#26B7DD', '#E8C634', '#19CED6', '#2FEAA3', '#252a3e', '#8d87a5', '#b9b784');
	}

	public static function get_default_duration_for_service($service_id){
		$service = new OsServiceModel($service_id);
		return $service->duration;
	}

  public static function service_option_html_for_select($service, $selected = false){
  	?>
  	<div class="service-option <?php if($selected) { echo 'selected'; } ?>" 
      data-extra-durations="<?php echo esc_attr(json_encode($service->get_extra_durations())); ?>"
      data-id="<?php echo $service->id; ?>" 
      data-buffer-before="<?php echo $service->buffer_before; ?>" 
      data-buffer-after="<?php echo $service->buffer_after; ?>" 
      data-capacity-min="<?php echo $service->capacity_min; ?>" 
      data-capacity-max="<?php echo $service->capacity_max; ?>" 
      data-duration="<?php echo $service->duration; ?>">
        <div class="service-color" style="background-color: <?php echo $service->bg_color; ?>"></div>
        <span><?php echo $service->name ?></span>
    </div>
    <?php
  }


  public static function get_services_list(){
    $services = new OsServiceModel();
    $services = $services->get_results_as_models();
    $services_list = [];
    if($services){
	    foreach($services as $service){
	      $services_list[] = ['value' => $service->id, 'label' => $service->name];
	    }
	  }
    return $services_list;
  }

	public static function generate_service_categories_list($parent_id = false){
    $service_categories = new OsServiceCategoryModel();
    $args = array();
    $args['parent_id'] = $parent_id ? $parent_id : 'IS NULL';
    $service_categories = $service_categories->where($args)->order_by('order_number asc')->get_results_as_models();
    if(!is_array($service_categories)) return;
    if($service_categories){
			foreach($service_categories as $service_category){ ?>
				<div class="os-category-parent-w" data-id="<?php echo $service_category->id; ?>">
					<div class="os-category-w">
						<div class="os-category-head">
							<div class="os-category-drag"></div>
							<div class="os-category-name"><?php echo $service_category->name; ?></div>
							<div class="os-category-services-meta"><?php _e('ID: ', 'latepoint'); ?><span><?php echo $service_category->id; ?></span></div>
							<div class="os-category-services-count"><span><?php echo $service_category->count_services(); ?></span> <?php _e('Services Linked', 'latepoint'); ?></div>
							<button class="os-category-edit-btn"><i class="latepoint-icon latepoint-icon-edit-3"></i></button>
						</div>
						<div class="os-category-body">
							<?php include(LATEPOINT_VIEWS_ABSPATH. 'service_categories/_form.php'); ?>
						</div>
					</div>
					<div class="os-category-children">
						<?php 
						if(is_array($service_category->services)){
							foreach($service_category->services as $service){
								echo '<div class="service-in-category-w status-'.$service->status.'" data-id="'.$service->id.'"><div class="os-category-service-drag"></div><div class="os-category-service-name">'.$service->name.'</div><div class="os-category-service-meta">ID: '.$service->id.'</div></div>';
							}
						} ?>
						<?php OsServiceHelper::generate_service_categories_list($service_category->id); ?>
					</div>
				</div>
				<?php
			}
		}
	}

}