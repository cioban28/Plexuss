



    //MajorCrumbList
    /**********************************************
    ***********************************************
    *   class majorCrumbList is a list containing 
    *   objects of type MajorCrumb
    *
    ***********************************************/
    function MajorCrumbList(){
        this.crumbs = [];
    }
    MajorCrumbList.prototype.length = function(){
        return this.crumbs.length;
    }; 
    MajorCrumbList.prototype.popCrumb = function(){
        return this.crumbs.pop();
    };
    MajorCrumbList.prototype.addTextCrumb = function(crumbText){
        //check if unique
        var crumb = new MajorCrumb(crumbText.trim());
        return this.crumbs.push(crumb);
    };
    MajorCrumbList.prototype.addCrumb = function(crumb){
        //check if unique
        return this.crumbs.push(crumb);
    };
    MajorCrumbList.prototype.removeCrumb = function(crumbText){
        
        //if found -- remove and return new list-- else return null
        
        var i = this.findCrumb(crumbText);
        
        if(i != -1)
            this.crumbs.splice(i, 1);
        else
            return null;

        return this.crumbs;
       
    };
    MajorCrumbList.prototype.findCrumb = function(crumbText){
      
        for(var i in this.crumbs){
            if(this.crumbs[i].major === crumbText.trim()){   
                return i;
            }
        }
        return -1;
    };
    MajorCrumbList.prototype.clearList = function(){
        this.crumbs.length = 0;
    };



    //MajorCrumb
    /**********************************************
    ***********************************************
    *   MajorCrumb object is a crumb with major information
    *
    ***********************************************/
    function MajorCrumb(pmajor){
        this.major = pmajor.trim();
    }
    MajorCrumb.prototype.getCrumb = function(){
         return '<div id="'+ this.major +'" class="major-crumb">' +
                 '<span class="crumb-name">' +
                    this.major+' '+
                 '</span><span class="obj-close-btn">&times;</span></div>'; 
    }





    /********************************************
    *   handler for adding majors to the objective 
    *   section of User Profile
    *   and adds crumbs to exisiting list
    *
        //get the text in the box
    *********************************************/
    function addMajorsHandler(e, majorsList){

        //cannot add more than four
        if(majorsList.length() === 4){
            return;

        }

        var el = $(e.target);

        var major = '';

        major = el.closest('.major-listing-cont').find('.major-name').text();

        //if empty still, just return
        if(major === ''){
            return;
        }

         //if crumb exist already, return
        if( majorsList.findCrumb(major) != -1){

            //display message
            $('#duplicate_crumb_error').css('display','inline');

            $(document).one('click', '#objMajor', function(){
                $('#duplicate_crumb_error').hide();
            });

            return;
        }

        //inject major into text string as 'crumb' with remove button
        var crumb = new MajorCrumb(major);
     
        majorsList.addCrumb(crumb);

        //if list is = 4 now -- let users know max has been reached
        if(majorsList.length() === 4){
            $('#max-note').css('display', 'block');

        }

        el.closest('.majors-container').find('.majors_crumb_list').append(crumb.getCrumb());
    };



    ///////////////////////////////////////////////////////
    function showMajorsList(e){
        var listCon = $(e.target).closest('.objMajorContainer').find('.majors-list-select');
        listCon.show();

        $(document).one('click', function(e){
                if(!$(e.target).hasClass('.majors-list-select') && !$(e.target).hasClass('#objMajor'))
                    listCon.hide();
            });
    }


    /************************************************************************
    *   opens the dropdown for some user feedback and starts to 
    *   get majors via ajax
    *************************************************************************/
    function getMajors(e){

            var listCon = $(e.target).closest(".objMajorContainer").find('.majors-list-select');
            var  plus, major;
            var pop = listCon.find('.popular');
            var other = listCon.find('.other');
            
            //show loading feedback if loading
            if($('#objMajor').val() === ''){
                pop.html('');
                other.html('');
            }else{
                pop.html('&nbsp;&nbsp;&nbsp;Loading...');
                other.html('&nbsp;&nbsp;&nbsp;Loading...');
            }

            ///// ajax to get majors
            $.ajax({
                url : '/ajax/profile/objective/searchFor/major',
                data: {input: $('#objMajor').val()},
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function(data){
                //console.log(data);

                var listing = '';

                //empty list to repopulate
                pop.html('');
                other.html('');

                if( data.popular_majors.length === 0){
                     listing = $('<div>', {'class': 'major-listing-cont'});
                     listing.text('No results.');
                }
                else{

                    listing = '';
                    for(var i in data.popular_majors){
                        listing +=  '<div class="major-listing-cont">' + 
                                    '<span class="major-plus"> + </span>' +
                                    '<span class="major-name">' + data.popular_majors[i].name + '</span></div>'; 
                    }
                }
                pop.html(listing);

                if( data.other_majors.length === 0){
                     listing = $('<div>', {'class': 'major-listing-cont'});
                     listing.text('No results.');
                }else{
                    
                    listing = '';
                    for(var i in data.other_majors){
                         listing += '<div class="major-listing-cont">' + 
                                    '<span class="major-plus"> + </span>' +
                                    '<span class="major-name">' + data.other_majors[i].name + '</span></div>'; 
                    }
                }
                other.html(listing);

            });
    }


