
Light = {
		state : 0, //0为初始状态，1为正在打开，2为已经打开，3为打开失败
		socket : null,
		timeLeft : 0,
		
		/*
		 * 管理当前的连接，每隔10秒发送一个心跳信号来保持连接
		 */
		monitorConnection : function() {
			var self = this;
			var connector = function() {
				self.timeLeft--;
				if (self.state == 2 && self.timeLeft <= 0) {
					self.sendMsg("heartbeat");
				}
			}
			window.setInterval(connector, 1000);
		},

		/*
		 * 向控制程序发送消息
		 */
		sendMsg  : function(msg) {
			var self = this;
			if (self.state == 0) {
				self.socket = new WebSocket('ws://localhost:8710');
				self.state = 1;
				self.socket.onopen = function(event) {
					self.state = 2;
					self.socket.send(msg);
					self.timeLeft = 10;
					//socket.onmessage = function(event) {
					//	console.log('Client received a message:' + event.data);
					//};
					self.monitorConnection();
				};
			} else if (self.state == 2) {
				self.timeLeft = 10;
				self.socket.send(msg);
			}
		},

		/*
		 * 打开指定编号的红色长亮LED
		 */
		openRed : function(number){
			this.sendMsg("openRed(" + number + ")");
		},

		/*
		 * 打开指定编号的绿色长亮LED
		 */
		openGreen : function(number){
			this.sendMsg("openGreen(" + number + ")");
		},

		/*
		 * 打开指定编号的红色闪烁LED
		 */
		flashRed : function(number){
			this.sendMsg("flashRed(" + number + ")");
		},

		/*
		 * 打开指定编号的绿色闪烁LED
		 */
		flashGreen : function(number){
			this.sendMsg("flashGreen(" + number + ")");
		},

		/*
		 * 关闭指定编号LED，红绿全关
		 */
		close : function(number){
			this.sendMsg("close(" + number + ")");   
		},

		/*
		 * 关闭所有的LED
		 */
		closeAll : function(){
			this.sendMsg("closeAll");
		},

		/*
		 * 设置LED的编号。在设置前，先按一下灯盒右上方的小按钮，
		 * 待LED开始闪烁后，调用此方法设置它的编号
		 */
		setNumber : function(number){
			this.sendMsg("setNumber(" + number + ")");
		},

		/*
		 * 红绿灯交替闪烁
		 */
		flashSupersede : function(number){
			this.sendMsg("flashSupersede("+ number + ")");
		}
}
