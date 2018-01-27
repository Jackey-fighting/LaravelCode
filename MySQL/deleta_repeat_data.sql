delete from tb where lastname in 
    (select * from 
        (select lastname from tb group by lastname having count(lastname)>1) tmp  ##这里是筛选出重复的 lastname列值
    ) and id not in 
    (select * from 
        (select min(id) as id from tb group by lastname having count(lastname)>1) tmp2  ##这里是筛选出重复 lastname列的最小id
    );
    
    ##因为 select lastname from tb group by lastname having count(lastname)>1 后是生成一个表格的，然后需要在 select * from tmp 
    也就是前面生成的表别名，然后 一定要使用 select * 星号来作为筛选出所有的，然后就可以作为条件删除了，亲测 用列名，会报错。
