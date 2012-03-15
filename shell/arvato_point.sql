#!/bin/bash 
# edit by justin.huang 11/25/2011
# get order num and modify date and put it into arvato_point
use sisley;
insert into arvato_point select `increment_id`,DATE_FORMAT(`updated_at`,'%Y-%m-%d') as d from sales_flat_order
where `status`='shipping'
and `increment_id` not in (select orderid from arvato_point);
select count(*)newrecord,CURDATE() c_date from arvato_point where date =CURDATE();
