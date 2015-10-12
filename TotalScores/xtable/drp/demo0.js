$(function()
{
	$('#date-range0').dateRangePicker(
	{
	})
	.bind('datepicker-change',function(event,obj)
	{
		/* This event will be triggered when second date is selected */
		console.log('change',obj);
		// obj will be something like this:
		// {
		// 		date1: (Date object of the earlier date),
		// 		date2: (Date object of the later date),
		//	 	value: "2013-06-05 to 2013-06-07"
		// }
	})
	.bind('datepicker-closed',function()
	{
		/* This event will be triggered after date range picker close animation */
		console.log('after close');
	})
});
