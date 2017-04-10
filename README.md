# 在线记事方案总结

## 第一版，PHP后台与表单交互过程
1. PHP输出HTML，难以维护，容易出错
2. 多次重复查询数据库，造成资源浪费

### TODO
1. 完成了Bootstrap布局，美化的基本使用
2. 艺人与影片没有联动

## 第二版，前后端分离
1. 前端使用JQuery获取表格内容，向后端发起请求，得到数据（状态）后更新网页
2. PHP只负责查询所需要的数据，组装成json输出
3. 功能更好独立，页面和逻辑分开处理
4. PHP 设置`header('Content-type: text/json');`$_POST中的数据就从JSON转换成PHP数组模式，判断是否有数据依然不变
5. PHP 输出方式变成`echo json_encode(result);`方式

### TODO
1. 完成了Movie搜索页面的重构，减少了一次请求
2. 确定了JQuery只有父节点才能查找子节点，而不能在兄弟节点间查找
3. 解决了搜索页面更新无法生效的问题
4. 未完成Mov页面的改造
5. 数据库应该有id字段，用来更新保存

 

## 第三版，使用Vue代替JQuery
1. 数据双向绑定，改动确认后使用axios发送到后端
2. 减少了很多代码量

### TODO
1. 评分推荐系统，tags标签
2. 分析最喜欢的观影口味，智能推荐（公司都是有一点相关就会吹，抢占先机，而我在担心恩能够不嗯那个完成）
3. 数据迁移到MySQL，稳定使用