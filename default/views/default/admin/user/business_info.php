<div class="page-header">
    <h1>
                                        个人资料
        <small>
            <i class="icon-double-angle-right"></i>
            查看个人信息
        </small>
    </h1>
</div>

<div class="row">
  
  <div class="col-xs-12 col-sm-9">
      
      <div class="profile-user-info">
          <div class="profile-info-row">
              <div class="profile-info-name">  商户名称 </div>

              <div class="profile-info-value">
                  <span><?php echo $list['name']?></span>
              </div>
          </div>
          
          <div class="profile-info-row">
              <div class="profile-info-name">  商户区域 </div>

              <div class="profile-info-value">
                  <span>
                  <?php foreach($region_name as $item):?>
                        <?php echo $item?> &nbsp;
                   <?php endforeach;?>
         		</span>
              </div>
          </div>

          <div class="profile-info-row">
              <div class="profile-info-name"> 地址 </div>

              <div class="profile-info-value">
                  <span><?php echo $list['company_address']?></span>
              </div>
          </div>
          
          <div class="profile-info-row">
              <div class="profile-info-name"> 联系电话 </div>

              <div class="profile-info-value">
                  <span><?php echo $list['company_tel']?></span>
              </div>
          </div>
          
          <div class="profile-info-row">
              <div class="profile-info-name"> 联系人 </div>

              <div class="profile-info-value">
                  <span><?php echo $list['corporate_name']?></span>
              </div>
          </div>
          
          <div class="profile-info-row">
              <div class="profile-info-name"> 创建时间 </div>

              <div class="profile-info-value">
                  <span><?php echo datetime($list['create_time'])?></span>
              </div>
          </div>

      </div>

      <div class="hr hr-8 dotted"></div>

  </div>
</div>