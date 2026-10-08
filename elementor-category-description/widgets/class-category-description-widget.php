<?php
if(!defined('ABSPATH'))exit;
class Elementor_Category_Description_Widget extends \Elementor\Widget_Base {
	public function get_name(){return 'category_description';}
	public function get_title(){return esc_html__('Category Description','elementor-category-description');}
	public function get_icon(){return 'eicon-post-content';}
	public function get_categories(){return array('general');}

	protected function get_taxonomies_options(){
		$o=array();
		if(function_exists('get_taxonomies')){
			foreach(get_taxonomies(array('public'=>true),'objects') as $t)$o[$t->name]=$t->label;
		}else{$o=array('category'=>'Categories','post_tag'=>'Tags','product_cat'=>'Product categories');}
		return $o;
	}

	protected function get_terms_options(){
		$o=array(''=>esc_html__('-- Select Term --','elementor-category-description'));
		if(function_exists('get_terms')){
			$taxes=function_exists('get_taxonomies')?array_keys(get_taxonomies(array('public'=>true))):array('category','post_tag','product_cat');
			$terms=get_terms(array('taxonomy'=>$taxes,'hide_empty'=>false));
			if(!is_wp_error($terms)&&!empty($terms)){
				foreach($terms as $t){
					$tx=function_exists('get_taxonomy')?get_taxonomy($t->taxonomy):null;
					$o[$t->term_id]=sprintf('%s (%s)',$t->name,$tx?$tx->labels->singular_name:$t->taxonomy);
				}
			}
		}
		return $o;
	}

	protected function register_controls(){
		$this->start_controls_section('section_content',array('label'=>esc_html__('Category Description','elementor-category-description'),'tab'=>\Elementor\Controls_Manager::TAB_CONTENT));
		$this->add_control('source',array('label'=>esc_html__('Source','elementor-category-description'),'type'=>\Elementor\Controls_Manager::SELECT,'default'=>'taxonomy_filter','options'=>array('taxonomy_filter'=>esc_html__('Elementor Taxonomy Filter / Query','elementor-category-description'),'loop_grid'=>esc_html__('Loop Grid / Product Category Sync','elementor-category-description'),'current'=>esc_html__('Current Query','elementor-category-description'),'custom'=>esc_html__('Select Taxonomy / Category','elementor-category-description'))));
		$this->add_control('taxonomy',array('label'=>esc_html__('Taxonomy Filter','elementor-category-description'),'type'=>\Elementor\Controls_Manager::SELECT,'default'=>'product_cat','options'=>$this->get_taxonomies_options(),'condition'=>array('source'=>array('custom','taxonomy_filter','loop_grid'))));
		$this->add_control('term_id',array('label'=>esc_html__('Category / Term','elementor-category-description'),'type'=>\Elementor\Controls_Manager::SELECT,'default'=>'','options'=>$this->get_terms_options(),'condition'=>array('source'=>'custom')));
		$this->add_control('html_tag',array('label'=>esc_html__('HTML Tag','elementor-category-description'),'type'=>\Elementor\Controls_Manager::SELECT,'default'=>'div','options'=>array('div'=>'div','p'=>'p','span'=>'span','h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4')));
		$this->add_control('enable_wpautop',array('label'=>esc_html__('Add Paragraphs','elementor-category-description'),'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'));
		$this->add_control('fallback_text',array('label'=>esc_html__('Fallback Text','elementor-category-description'),'type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>''));
		$this->end_controls_section();

		$this->start_controls_section('section_style',array('label'=>esc_html__('Description Style','elementor-category-description'),'tab'=>\Elementor\Controls_Manager::TAB_STYLE));
		$this->add_responsive_control('align',array('label'=>esc_html__('Alignment','elementor-category-description'),'type'=>\Elementor\Controls_Manager::CHOOSE,'options'=>array('left'=>array('title'=>'Left','icon'=>'eicon-text-align-left'),'center'=>array('title'=>'Center','icon'=>'eicon-text-align-center'),'right'=>array('title'=>'Right','icon'=>'eicon-text-align-right'),'justify'=>array('title'=>'Justify','icon'=>'eicon-text-align-justify')),'default'=>'left','selectors'=>array('{{WRAPPER}} .elementor-category-description'=>'text-align: {{VALUE}};')));
		$this->add_control('text_color',array('label'=>esc_html__('Text Color','elementor-category-description'),'type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .elementor-category-description'=>'color: {{VALUE}};')));
		if(class_exists('\Elementor\Group_Control_Typography'))$this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'typography','selector'=>'{{WRAPPER}} .elementor-category-description'));
		$this->add_responsive_control('padding',array('label'=>esc_html__('Padding','elementor-category-description'),'type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px','em','%'),'selectors'=>array('{{WRAPPER}} .elementor-category-description'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
		$this->add_responsive_control('margin',array('label'=>esc_html__('Margin','elementor-category-description'),'type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>array('px','em','%'),'selectors'=>array('{{WRAPPER}} .elementor-category-description'=>'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};')));
		$this->end_controls_section();
	}

	public function get_description_text($settings){
		$d='';$src=isset($settings['source'])?$settings['source']:'taxonomy_filter';
		$tax=isset($settings['taxonomy'])?$settings['taxonomy']:'category';

		if('custom'===$src&&!empty($settings['term_id'])){
			$tid=(int)$settings['term_id'];
			$d=function_exists('term_description')?term_description($tid):(function_exists('get_term')&&($t=get_term($tid))&&!is_wp_error($t)?$t->description:'');
		}elseif('taxonomy_filter'===$src||'loop_grid'===$src){
			$sel=null;
			if(!empty($_GET)){
				foreach($_GET as $k=>$v){
					if((0===strpos($k,'e-filter-')||$k===$tax||$k==='product_cat'||$k==='category')&&!empty($v)){$sel=sanitize_text_field($v);break;}
				}
			}
			if($sel&&function_exists('get_term_by')){
				$to=is_numeric($sel)?get_term((int)$sel,$tax):get_term_by('slug',$sel,$tax);
				if(!$to&&'product_cat'!==$tax)$to=get_term_by('slug',$sel,'product_cat');
				if($to&&!is_wp_error($to))$d=function_exists('term_description')?term_description($to->term_id):$to->description;
			}
			if(empty($d)&&function_exists('get_queried_object')){
				$qo=get_queried_object();
				if($qo&&isset($qo->term_id))$d=function_exists('term_description')?term_description($qo->term_id):(isset($qo->description)?$qo->description:'');
			}
		}else if(function_exists('get_queried_object')){
			$qo=get_queried_object();
			if($qo&&isset($qo->term_id))$d=function_exists('term_description')?term_description($qo->term_id):(isset($qo->description)?$qo->description:'');
		}

		if(empty(trim(strip_tags((string)$d))))$d=!empty($settings['fallback_text'])?$settings['fallback_text']:'';
		if(!empty($d)&&isset($settings['enable_wpautop'])&&'yes'===$settings['enable_wpautop']&&function_exists('wpautop'))$d=wpautop($d);
		return $d;
	}

	protected function get_all_term_descriptions($tax='category'){
		$m=array();
		if(function_exists('get_terms')){
			$terms=get_terms(array('taxonomy'=>array_unique(array_filter(array($tax,'product_cat','category','post_tag'))),'hide_empty'=>false));
			if(!is_wp_error($terms)&&!empty($terms)){
				foreach($terms as $t){
					$desc=function_exists('term_description')?term_description($t->term_id):$t->description;
					if(!empty($desc)){
						if(function_exists('wpautop'))$desc=wpautop($desc);
						$m[$t->slug]=$desc;$m[$t->term_id]=$desc;$m['cat-'.$t->term_id]=$desc;$m['cat-'.$t->slug]=$desc;
					}
				}
			}
		}
		return $m;
	}

	protected function render(){
		$s=$this->get_settings_for_display();
		$d=$this->get_description_text($s);
		$tags=array('div','p','span','h1','h2','h3','h4');
		$tag=in_array(isset($s['html_tag'])?$s['html_tag']:'div',$tags,true)?$s['html_tag']:'div';
		$wid=$this->get_id();
		$tax=isset($s['taxonomy'])?$s['taxonomy']:'category';
		$fb=isset($s['fallback_text'])?$s['fallback_text']:'';

		if(empty($d)&&class_exists('\Elementor\Plugin')&&isset(\Elementor\Plugin::$instance->editor)&&\Elementor\Plugin::$instance->editor->is_edit_mode()){
			printf('<%1$s class="elementor-category-description" style="padding:10px;border:1px dashed #ccc;text-align:center;color:#888;">%2$s</%1$s>',esc_attr($tag),esc_html__('No category description.','elementor-category-description'));
			return;
		}

		$cd=function_exists('wp_kses_post')?wp_kses_post($d):$d;
		printf('<%1$s id="elementor-category-description-%3$s" class="elementor-category-description" data-widget-id="%3$s">%2$s</%1$s>',esc_attr($tag),$cd,esc_attr($wid));

		$flags=defined('JSON_HEX_TAG')?JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT:0;
		$jmap=function_exists('wp_json_encode')?wp_json_encode($this->get_all_term_descriptions($tax),$flags):json_encode($this->get_all_term_descriptions($tax),$flags);
		$jfb=function_exists('wp_json_encode')?wp_json_encode($fb,$flags):json_encode($fb,$flags);
		?>
		<script>
		(function(){
			var tm=<?php echo $jmap?$jmap:'{}';?>;var fb=<?php echo $jfb?$jfb:'""';?>;
			function update(v){
				var c=document.getElementById('elementor-category-description-<?php echo esc_js($wid);?>');if(!c)return;
				if(v)v=v.toString().trim().replace(/^\./,'').replace(/^cat-/,'');
				var d=tm[v]||tm['cat-'+v]||fb||'';c.innerHTML=d;c.style.display=d?'':'none';
			}
			document.addEventListener('click',function(e){
				var i=e.target.closest('[data-filter],.e-filter-item,[data-term-id],[data-term-slug],.product-category,.elementor-loop-container a');
				if(i){
					var v=i.getAttribute('data-filter')||i.getAttribute('data-term-slug')||i.getAttribute('data-term-id');
					if(!v&&i.getAttribute('href')){
						var m=i.getAttribute('href').match(/\/(product-category|category)\/([^\/]+)/);if(m&&m[2])v=m[2];
					}
					if(v)update(v);
				}
			});
		})();
		</script>
		<?php
	}

	protected function content_template(){
		?>
		<# var tag=settings.html_tag||'div'; #>
		<{{{ tag }}} class="elementor-category-description">Category Description Preview</{{{ tag }}}>
		<?php
	}
}
