                <div class="sidebar" id="sidebar">
                    <script type="text/javascript">
                        try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
                    </script>

                    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
                        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                            <button class="btn btn-success">
                                <i class="icon-signal"></i>
                            </button>

                            <button class="btn btn-info">
                                <i class="icon-pencil"></i>
                            </button>

                            <button class="btn btn-warning">
                                <i class="icon-group"></i>
                            </button>

                            <button class="btn btn-danger">
                                <i class="icon-cogs"></i>
                            </button>
                        </div>
                        <DIV CLASS="SIDEBAR-SHORTCUTS-MINI" ID="SIDEBAR-SHORTCUTS-MINI">
                            <SPAN CLASS="BTN BTN-SUCCESS"></SPAN>

                            <SPAN CLASS="BTN BTN-INFO"></SPAN>

                            <SPAN CLASS="BTN BTN-WARNING"></SPAN>

                            <SPAN CLASS="BTN BTN-DANGER"></SPAN>
                        </DIV>
                    </div><!-- #sidebar-shortcuts -->

                    <ul class="nav nav-list">
                        
                        <?php foreach($this->menu_tree as $item):?>
	                    <li class="<?php if ($this->current_menu == $item['cid'] || array_key_exists($this->current_menu,$item['child'])):?> active <?php endif;?> <?php if (array_key_exists($this->current_menu,$item['child'])):?> open <?php endif;?>">
	                        <a <?php if($item['child']):?> href="javascript:" class="dropdown-toggle" <?php else:?> href="<?php echo admin_base_url($item['con_name'],$item['directory'])?>" <?php endif;?>>
                                <i class="<?php echo $item['params']->get('class_sfx','')?>"></i>
                                <span class="menu-text"> <?php echo $item['title']?></span>
								<?php if ($item['child']):?>
                                <b class="arrow icon-angle-down"></b>
                                <?php endif;?>
                            </a>
	                        <?php if ($item['child']):?>
	                        <ul class="submenu">
	                            <?php foreach ($item['child'] as $child):?>
                                <li <?php if ($this->current_menu == $child['cid']):?> class="active"<?php endif;?>>
                                    <a href="<?php echo admin_base_url($child['con_name'],$child['directory'])?>">
                                        <i class="icon-double-angle-right"></i>
                                        <?php echo $child['title']?>
                                    </a>
                                </li>
	                            <?php endforeach;?>
                            </ul>
	                        <?php endif;?>
	                    </li>
	                    <?php endforeach;?>
                    </ul><!-- /.nav-list -->

                    <div class="sidebar-collapse" id="sidebar-collapse">
                        <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
                    </div>

                    <script type="text/javascript">
                        try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
                    </script>
                </div>
                <div class="main-content">
                    <div class="breadcrumbs" id="breadcrumbs">
                        <script type="text/javascript">
                            try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
                        </script>

                        <ul class="breadcrumb">
                            <?php
                            $i = 0; 
                            $c = count($this->breadcrumb);
                            foreach ($this->breadcrumb as $link):
                            ?>
                            <li <?php if ($i==$c):?> class="active"<?php endif;?>>
                                <?php if (!$i):?>
							    <i class="icon-home home-icon"></i>
							    <?php endif;?>
                                <?php echo $link?>
                            </li>
                            <?php 
                                $i++;
                            endforeach;
                            ?>
                        </ul><!-- .breadcrumb -->
<!--
                        <div class="nav-search" id="nav-search">
                            <form class="form-search">
                                <span class="input-icon">
                                    <input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
                                    <i class="icon-search nav-search-icon"></i>
                                </span>
                            </form>
                        </div>
-->
                        <!-- #nav-search -->
                    </div> 
                    <!--内容-->
                    <div class="page-content">
                        <div class="row">
                           <div class="col-xs-12">