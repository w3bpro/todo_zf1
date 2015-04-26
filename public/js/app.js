function app() {
	this.currentListID = -1;
	this.init = function() {
		console.log('app init');
		this.bindEvents();
		this.windowResize();
		this.initProjectList();
		this.getLists(true);
	}

	this.bindEvents = function() {
		var that = this;
		$('#add-new-list').bind('click', function() {
			that.addNewList();
		});

		$('#add-task').bind('click', function() {
			that.addNewTask();
		});

		$('body').on('click', '.open-list', function() {
			$('.open-list').removeClass('active');
			$(this).addClass('active');
			that.currentListID = $(this).data('id');
			that.getTasks($(this).data('id'));
		});

		$('body').on('click', '.task-update', function() {
			that.updateTask($(this).data('id'), $(this).parent().find('.task-body').val());
		});

		$('body').on('click', '.task-delete', function() {
			that.deleteTask($(this).data('id'));
		});

		$('body').on('click', '.mark-done', function() {
			that.markAsDone($(this).data('id'));
		});
	}

	this.viewport =  function(){
	    var e = window, a = 'inner';
	    if (!('innerWidth' in window )) {
	        a = 'client';
	        e = document.documentElement || document.body;
	    }
	    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
	}

	this.windowResize =  function() {
	}

	this.initProjectList = function() {
		var newHeight = this.viewport().height - $('header').outerHeight();
		$('#project-list').height(newHeight);
	}

	this.addNewList =  function() {
		swal({   
			title: "Add new list",   
			text: "Write name for your list:",  
			type: "input",   showCancelButton: true,   
			closeOnConfirm: false,   
			animation: "slide-from-top",   
			inputPlaceholder: "Name of my list" 
			}, 
			function(inputValue){   
				if (inputValue === false) return false;      
				if (inputValue === "") {     
					swal.showInputError("You need to write something!");     
					return false  
				}

	            $.ajax({type:'POST', dataType: 'json', url: baseUrl + '/list/', data:{ name: inputValue},
	                success: function(response){

	                    if(response.result == 'ok') {
	                      	$('#project-list ul').append('<li><a data-id="' + response.result.listId + '">' + inputValue + ' (0)</a></li>');
	                      	swal("New list added!", "Congratulations! Now, you can add tasks to your list.", "success");
	                    }
	                    else if(response.result == 'error') {
	                        swal("Add a new list", "Something went wrong, try again.", "error");
	                    }
	                },
	                beforeSend:function(){
	                    //$btn.empty().append('<i class="fa fa-spinner fa-spin fa-lg"></i>');
	                }
	            });
			}
		);
	}

	this.addNewTask = function() {
		var task = $('#new-task-body').val();
		var $btn = $('#add-task');
		var html = $btn.html();
		if($btn.hasClass('disabled') ) {
			$btn.addClass('disabled');
		}
		$.ajax({type:'POST', dataType: 'json', url: baseUrl + '/task/', data:{ task: task, list_id: this.currentListID},
            success: function(response){
            	$btn.removeClass('disabled').html(html);
                if(response.result == 'ok') {
                	$('#tasks-list .alert').remove();
                  	$('#tasks-list ul').prepend('<li><div class="check"></div><input data-id="' + response.result.taskID + '" value="' + task +'" class="form-control"/><button class="btn btn-primary">Update</button><button class="btn btn-warning">Delete</button></li>');
                  	//swal("New list added!", "Congratulations! Now, you can add tasks to your list.", "success");
                }
                else if(response.result == 'error') {
                    swal("Add a new task", "Something went wrong, try again.", "error");
                }
            },
            beforeSend:function(){
            	$btn.empty().append('<i class="fa fa-spinner fa-spin fa-lg"></i>');
            }
        });
	}

	this.getLists = function(first) {
		if(typeof first == 'undefined') {
			first =  false;
		}

		$.ajax({type:'GET', dataType: 'json', url: baseUrl + '/list/',
            success: function(response){

                if(response.result == 'ok') {
                	$('#project-list ul').empty();
                	for( i = 0; i < response.data.length; i++) {
                		var row = response.data[i];
                		$('#project-list ul').append('<li><a class="open-list" data-id="' + row.list_id + '">' + row.name + '</a></li>');
                	}

                	if(first) {
            			$('#project-list ul').find('.open-list').eq(0).trigger('click');
                	}
                }

               
            },
            beforeSend:function(){
            }
        });
	}

	this.getTasks = function() {
		$.ajax({type:'GET', dataType: 'json', url: baseUrl + '/task/', data: {list_id: this.currentListID},
            success: function(response){

                if(response.result == 'ok') {
                	$('#tasks-list ul').empty();
                	for( i = 0; i < response.data.length; i++) {
                		var row = response.data[i];
                		$('#tasks-list ul').prepend('<li id="task-' + row.task_id + '"><input type="checkbox" class="form-control mark-done" data-id="' + row.task_id +'"/><input type="text" data-id="' + row.task_id + '" value="' + row.body +'" class="form-control task-body"/><button class="btn btn-primary task-update"  data-id="' + row.task_id + '">Update</button><button class="btn btn-warning task-delete" data-id="' + row.task_id + '">Delete</button></li>');

                		if(row.status == 'DONE') {
                			$('#task-' + row.task_id).addClass('done').find('.mark-done').prop('checked','checked');
                		}
                	}
                }
                else {
                	$('#tasks-list .alert').remove();
                	$('#tasks-list ul').empty().append('<div class="alert alert-dismissible alert-warning">Empty list</div>');
                }
               
            },
            beforeSend:function(){
            }
        });
	}
	this.updateTask = function(taskID, body) {

		var $btn = $('#task-' + taskID).find('.task-update');
		var html = $btn.html();
		if($btn.hasClass('disabled') ) {
			$btn.addClass('disabled');
		}
		$.ajax({type:'PUT', dataType: 'json', url: baseUrl + '/task/', data: {task_id: taskID, body: body},
            success: function(response){
            	$btn.removeClass('disabled').html(html);
                if(response.result == 'ok') {
                	
                }
                else {
        	 		swal("Update the task", "Something went wrong, try again.", "error");
                }
               
            },
            beforeSend:function(){
            	$btn.empty().append('<i class="fa fa-spinner fa-spin fa-lg"></i>');
            }
        });
	}
	this.deleteTask = function(taskID) {
		var that = this;
		var $btn = $('#task-' + taskID).find('.task-delete');
		var html = $btn.html();
		if($btn.hasClass('disabled') ) {
			$btn.addClass('disabled');
		}
		$.ajax({type:'DELETE', dataType: 'json', url: baseUrl + '/task/' + taskID,
            success: function(response){
            	$btn.removeClass('disabled').html(html);
                if(response.result == 'ok') {
                	that.getTasks();
                }
                else {
        	 		swal("Delete the task", "Something went wrong, try again.", "error");
                }
               
            },
            beforeSend:function(){
            	$btn.empty().append('<i class="fa fa-spinner fa-spin fa-lg"></i>');
            }
        });
	}

	this.markAsDone = function(taskID) { 
		var that = this;
		var checked = $('#task-' + taskID).find('.mark-done').eq(0).is(':checked');
		if(checked) {
			checked = 'DONE';
		}
		else {
			checked = 'TODO';
		}

		$.ajax({type:'POST', dataType: 'json', url: baseUrl + '/ajax/task/mark', data: {task_id: taskID, status: checked},
            success: function(response){
                if(response.result == 'ok') {
                	that.getTasks();
                }
                else {
        	 		swal("Mark task as done", "Something went wrong, try again.", "error");
                }
            },
            beforeSend:function(){
            }
        });

        return true;
	}
}
$(document).ready( function() {
	var td = new app;
	td.init();
});
