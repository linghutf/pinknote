//常用函数
function clone(obj)
{
   var newobj = {};
   for ( var attr in obj) {
        newobj[attr] = obj[attr];
        
   }
   return newobj;
}