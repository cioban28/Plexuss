import React, { Component } from 'react';
import { connect } from 'react-redux';
import { SelectedFilter } from './SelectedFilter.jsx';
import RecommendationMeter from './RecommendationMeter.jsx';
import { getAllDepartments, getDepartmentMajors } from '../../../../../actions/scholarshipsActions';
import _ from 'lodash';
import { setRecommendationFilter, resetRecommendationFilter } from '../../../../../actions/newsfeedActions';


const tabName = 'majorDeptDegree';
class Majors extends Component {
  constructor(props) {
    super(props);

    const state = (this.props.filterChanges.tabName === tabName && this.props.filterChanges.state) || this.props.majorsData;

    this.state = { ...state };

    this.handleDepartmentSelectionChange = this.handleDepartmentSelectionChange.bind(this);
    this.handleMajorSelectionChange = this.handleMajorSelectionChange.bind(this);
    this.handleRemoveFilter = this.handleRemoveFilter.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleResetFilterClick = this.handleResetFilterClick.bind(this);
  }

  componentDidMount() {
    this.props.getAllDepartments();
  }

  componentWillUnmount() {
    if(!_.isEqual(this.state, this.props.majorsData)) {
      const filter = {};
      filter.formData = this.buildFormData();
      filter.state = { ...this.state };
      filter.tabName = tabName;
      this.props.setFilterCurrentChanges(filter);
    }
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.resetFilter) {
      this.props.setResetTargetingFilter(false);
      this.setState({ ...nextProps.majorsData });
    }
  }

  handleDepartmentFilterChange(selectedFilter) {
    const departmentFilter = { ...this.state.departmentFilter };
    
    Object.keys(departmentFilter).forEach(filter => {
      if(filter === selectedFilter) {
        departmentFilter[filter] = !departmentFilter[filter];
      } else {
        departmentFilter[filter] = false;
      }
    });

    this.setState({ departmentFilter });
  }

  handleDeptMajorFilterChange(selectedFilter, index) {
    const selectedDepartments = _.cloneDeep(this.state.selectedDepartments);
    
    Object.keys(selectedDepartments[index].majorFilter).forEach(key => {
      if(key === selectedFilter) {
        selectedDepartments[index].majorFilter[key] = !selectedDepartments[index].majorFilter[key];
      } else {
        selectedDepartments[index].majorFilter[key] = false;
      }
    });

    this.setState({ selectedDepartments });
  }

  handleDepartmentSelectionChange(e) {
    const selectedDepartment = this.props.departments[e.target.value];
    const selectedDepartments = [...this.state.selectedDepartments];
    if(selectedDepartments.some(selectedDept => selectedDept.name === selectedDepartment.name)) {
      return;
    }
    this.props.getDepartmentMajors(selectedDepartment.id);
    selectedDepartment.majorFilter = { all: true, include: false, exclude: false };
    selectedDepartment.degreeFilters = {...this.state.degreeFilters};
    selectedDepartments.push(selectedDepartment);
    this.setState({ selectedDepartments, selectedDeptName: selectedDepartment.name });
  }

  handleMajorSelectionChange(index, e) {
    const selectedDepartments = _.cloneDeep(this.state.selectedDepartments);
    const selectedDept = selectedDepartments[index];
   
    let selectedMajors = selectedDept.selectedMajors ? [...selectedDept.selectedMajors] : [];
    let majorId = '';
    for(let majors of this.props.deptsMajors) {
      const majorIndex = Object.values(majors).indexOf(e.target.value);
      if(majorIndex !== -1) {
        majorId = Object.keys(majors)[majorIndex];
        break;
      }
    }
    selectedMajors.push({id: majorId, name: e.target.value, degreeFilters: {...selectedDept.degreeFilters}});
    selectedDept.selectedMajors = selectedMajors;
    selectedDepartments[index] = selectedDept;
   
    this.setState({ selectedDepartments }); 
  }

  handleRemoveDepartmentClick(deptId) {
    const selectedDepartments = [ ...this.state.selectedDepartments ];
   
    const selectedDeptName = this.state.selectedDepartments.length === 1 ? '' : this.state.selectedDeptName;  
   
    this.setState({selectedDepartments: selectedDepartments.filter(dept => dept.id !== deptId), selectedDeptName});
  }

  handleRemoveDeptMajorClick(selectedDeptIndex, selectedMajorIndex) {
    const selectedDepartments = _.cloneDeep(this.state.selectedDepartments);
    const selectedDepartment = _.cloneDeep(this.state.selectedDepartments[selectedDeptIndex]);
   
    selectedDepartment.selectedMajors.splice(selectedMajorIndex, 1);
    selectedDepartments[selectedDeptIndex] = {...selectedDepartment};
   
    this.setState({ selectedDepartments }); 
  }

  handleToggleDeptClick(index) {
    const selectedDepartments = [...this.state.selectedDepartments];
    const selectedDepartment = {...selectedDepartments[index]};

    selectedDepartment.expanded = selectedDepartment.expanded ? !selectedDepartment.expanded : true;
    selectedDepartments[index] = selectedDepartment;

    this.setState({ selectedDepartments });
  }

  handleDegreeLevelChange(degreeName, selectedDeptIndex, selectedMajorIndex) {
    const selectedDepartments = _.cloneDeep(this.state.selectedDepartments);
    const selectedDepartment = _.cloneDeep(this.state.selectedDepartments[selectedDeptIndex]);

    if(selectedMajorIndex || selectedMajorIndex === 0) {
      if(selectedDepartment.selectedMajors && selectedDepartment.selectedMajors.length) {
        let selectedMajors = [...selectedDepartment.selectedMajors];
        selectedMajors[selectedMajorIndex].degreeFilters[degreeName] = !selectedMajors[selectedMajorIndex].degreeFilters[degreeName];
        selectedDepartment.selectedMajors = [...selectedMajors];
      }
    } else if(selectedDeptIndex || selectedDeptIndex === 0) {
      let selectedMajors = selectedDepartment.selectedMajors ? [...selectedDepartment.selectedMajors] : [];
      selectedDepartment.degreeFilters[degreeName] = !selectedDepartment.degreeFilters[degreeName];
      !!selectedDepartment.selectedMajors && !!selectedDepartment.selectedMajors.length && selectedDepartment.selectedMajors.forEach(major => {
        major.degreeFilters[degreeName] = !major.degreeFilters[degreeName];
      });
      selectedDepartment.selectedMajors = [...selectedMajors];
    } else {
      const degreeFilters = {...this.state.degreeFilters};

      degreeFilters[degreeName] = !degreeFilters[degreeName];
      !!selectedDepartments && !!selectedDepartments.length && selectedDepartments.forEach(dept => {
        dept.degreeFilters[degreeName] = degreeFilters[degreeName];
        !!dept.selectedMajors && !!dept.selectedMajors.length && dept.selectedMajors.forEach(major => {
          major.degreeFilters[degreeName] = degreeFilters[degreeName]
        });
      });

      this.setState({ degreeFilters });
    }

    selectedDepartments[selectedDeptIndex] = selectedDepartment;
    this.setState({ selectedDepartments }); 
  }

  handleRemoveFilter(filter, filterName) {
    let selectedDepartments = _.cloneDeep(this.state.selectedDepartments);
    
    if(filter === 'department') {
      selectedDepartments = selectedDepartments.filter(dept => dept.name !== filterName);
    } else {
      selectedDepartments = selectedDepartments.filter(dept => dept.selectedMajors = dept.selectedMajors.filter(major => major.name !== filterName));
    }
    
    this.setState({ selectedDepartments });
  }

  handleResetFilterClick() {
    this.props.resetRecommendationFilter(tabName);
  }

  handleSubmit(e) {
    e.preventDefault();
    
    this.props.setRecommendationFilterMajor({ ...this.state });
    this.props.setRecommendationFilter(tabName, this.buildFormData());    
  }

  buildFormData() {
    const { selectedDepartments, degreeFilters, departmentFilter } = this.state;
    const data = [];
    for(let [deptIndex, selectedDept] of selectedDepartments.entries()) {
      if(selectedDept.selectedMajors) {
        for(let selectedMajor of selectedDept.selectedMajors) {
          Object.keys(selectedMajor.degreeFilters).forEach((filter, index) => {
            if(selectedMajor.degreeFilters[filter]) {
              const obj = {};
              obj.department_id = selectedDept.id;
              obj.major_id = selectedMajor.id;
              obj.in_ex = selectedDept.majorFilter['include'] ? 'include' : 'exclude';
              obj.degree_id = index+1;
              data.push(obj);
            }
          }); 
        }
      } else {
        Object.keys(degreeFilters).forEach((filter, index) => {
          if(degreeFilters[filter]) {
            const obj = {};
            obj.department_id = selectedDept.id;
            obj.major_id = '';
            obj.in_ex = departmentFilter['include'] ? 'include' : 'exclude';
            obj.degree_id = index+1;
            data.push(obj);
          }
        });
      }
    }
    const values = { data: data, sales_pid: this.props.salesPostId };

    const formData = new FormData();
    Object.keys(values).forEach(key => {
      if(key === 'data') {
        Object.values(values[key]).forEach((value, dataIndex) => {
          const singleFilter = Object.keys(value);
          singleFilter.forEach((filterName, singleFilterIndex) => {
            formData.append(`data[${dataIndex}][${singleFilter[singleFilterIndex]}]`, value[singleFilter[singleFilterIndex]]);
          });
        })
      } else {
        formData.append(key, values[key]);
      }
    });
    return formData;
  }

  render() {
    const { departmentFilter, selectedDepartments, degreeFilters } = this.state;
    const { departments, deptsMajors, isLoading } = this.props;

    return (
      <form onSubmit={this.handleSubmit}>
        <div className='selected-filters-list'>
          <span className='filter-list-title'>Major: </span>
          {
            !!selectedDepartments && !!selectedDepartments.length && selectedDepartments.map((dept, index) => <span key={`${dept.name}-${index}-selected-dept`}>
              <SelectedFilter
                filter='department'
                filterName={dept.name}
                include={departmentFilter.include}
                exclude={departmentFilter.exclude}
                handleRemoveFilter={this.handleRemoveFilter}
              />
              {
                !!dept.selectedMajors && !!dept.selectedMajors.length && dept.selectedMajors.map((major, index) => <SelectedFilter
                  filter='major'
                  key={`${major.name}-${index}-selected-major`}
                  filterName={major.name}
                  include={dept.majorFilter.include}
                  exclude={dept.majorFilter.exclude}
                  handleRemoveFilter={this.handleRemoveFilter}
                />)
              }
            </span>)
          }
        </div>

        <RecommendationMeter />
        
        <div className='filtering-cont'>
          <div className='row'>
            <div className='columns small-12 large-6'>
              <div className='row mb-10'>
                <div className='columns small-12'>
                  <h4 className='filter-heading'>Department:</h4>
                  <p className='description-para'>Choose one option</p>
                  <input id='all_department_filter' type='radio' name='department' checked={departmentFilter.all} onChange={this.handleDepartmentFilterChange.bind(this, 'all')} />
                  <label htmlFor='all_department_filter' className='filter-label'>All</label>
                  <input id='include_department_filter' type='radio' name='department' checked={departmentFilter.include} onChange={this.handleDepartmentFilterChange.bind(this, 'include')} />
                  <label htmlFor='include_department_filter' className='filter-label'>Include</label>
                  <input id='exlude_department_filter' type='radio' name='department' checked={departmentFilter.exclude}  onChange={this.handleDepartmentFilterChange.bind(this, 'exclude')} />
                  <label htmlFor='exlude_department_filter' className='filter-label'>Exclude</label>
                </div>
              </div>
              {
                (departmentFilter.include || departmentFilter.exclude) && <div>
                  <p className='description-para'>You can select multiple options, just click to add</p>
                  <select onChange={this.handleDepartmentSelectionChange}>
                    <option defaultValue>Select...</option>
                    {
                      !!departments && !!departments.length && departments.map((department, index) => <option 
                        key={`${index}-${department.name}`} 
                        value={index}
                      >
                        {department.name}
                      </option>)
                    }
                  </select>
                </div>
              }
            </div>
            <div className='columns small-12 large-6 show-for-large-up'>
              <p className='description-para'>If your school is targeting students within a specific major, select the desired majors you'd like to include or exclude. You can select more than one item. These majors are from the same list we give students to choose from on their profiles.</p>
            </div>
            {
              !departmentFilter.all && !!selectedDepartments && !!selectedDepartments.length && <div className='columns small-12 dept-majors-list'>
                <div className='row'>
                  <div className='columns large-6 medium-6'></div>
                  <div className='columns large-6 medium-6 small-12'>
                    <p className='description-para'>Select degree level for all departments</p>
                    <div className='row'>
                      <div className='dept-degree-opts'>
                        <input 
                          type='checkbox' 
                          name='certificate-program' 
                          id='certificate-program'
                          checked={degreeFilters.certificateProgram} 
                          onChange={this.handleDegreeLevelChange.bind(this, 'certificateProgram', undefined, undefined)} 
                        />
                      </div>
                      <div className='dept-degree-opts'>
                        <input 
                          type='checkbox' 
                          name='associates'
                          id='associates'
                          checked={degreeFilters.associates} 
                          onChange={this.handleDegreeLevelChange.bind(this, 'associates', undefined, undefined)} 
                        />
                      </div>
                      <div className='dept-degree-opts'>
                        <input 
                          type='checkbox' 
                          name='bachelors'
                          id='bachelors'
                          checked={degreeFilters.bachelors} 
                          onChange={this.handleDegreeLevelChange.bind(this, 'bachelors', undefined, undefined)} 
                        />
                      </div>
                      <div className='dept-degree-opts'>
                        <input 
                          type='checkbox' 
                          name='masters'
                          id='masters'
                          checked={degreeFilters.masters} 
                          onChange={this.handleDegreeLevelChange.bind(this, 'masters', undefined, undefined)} 
                        />
                      </div>
                      <div className='dept-degree-opts'>
                        <input 
                          type='checkbox' 
                          name='doctorate'
                          id='doctorate'
                          checked={degreeFilters.doctorate} 
                          onChange={this.handleDegreeLevelChange.bind(this, 'doctorate', undefined, undefined)} 
                        />
                      </div>
                    </div>
                  </div>
                </div>
                <div className='row'>
                  <div className='columns large-6 medium-6'>
                    <label>Filter is including students interested in:</label>
                  </div>
                  <div className='columns large-6 medium-6 small-12'>
                    <div className='row vertically-center-items'>
                      <div className='dept-degree-label'>
                        <label htmlFor='certificate-program'>Certificate Program</label>
                      </div>
                      <div className='dept-degree-label'>
                        <label htmlFor='associates'>Associate's</label>
                      </div>
                      <div className='dept-degree-label'>
                        <label htmlFor='bachelors'>Bachelor's</label>
                      </div>
                      <div className='dept-degree-label'>
                        <label htmlFor='masters'>Master's</label>
                      </div>
                      <div className='dept-degree-label'>
                        <label htmlFor='doctorate'>Doctorate</label>
                      </div>
                    </div>
                  </div>
                </div>
                {
                  selectedDepartments.map((selectedDepartment, index) => <div key={`${selectedDepartment.name}-${index}`} className='dept-majors-item'>
                    <div className='row'>
                      <div className='columns large-6 medium-12 small-12 dept-action'>
                        <div className='remove-dept' onClick={this.handleRemoveDepartmentClick.bind(this, selectedDepartment.id)}><div>✖</div></div>
                        <div className='dept-name'>{selectedDepartment.name}</div>
                        <div className={`show-major ${selectedDepartment.expanded ? 'open' : ''}`} onClick={this.handleToggleDeptClick.bind(this, index)}><div className='arrow'></div></div>
                      </div>
                      <div className='columns large-6 medium-12 small-12'>
                      {
                        !selectedDepartment.selectedMajors && <div className='row'>
                          <div className='dept-degree-opts'>
                            <input 
                              type='checkbox' 
                              name={`certificate-program[${index}]`} 
                              checked={selectedDepartment.degreeFilters.certificateProgram} 
                              onChange={this.handleDegreeLevelChange.bind(this, 'certificateProgram', index, undefined)}
                            />
                          </div>
                          <div className='dept-degree-opts'>
                            <input 
                              type='checkbox' 
                              name={`associates[${index}]`}
                              checked={selectedDepartment.degreeFilters.associates} 
                              onChange={this.handleDegreeLevelChange.bind(this, 'associates', index, undefined)} 
                            />
                          </div>
                          <div className='dept-degree-opts'>
                            <input 
                              type='checkbox' 
                              name={`bachelors[${index}]`}
                              checked={selectedDepartment.degreeFilters.bachelors} 
                              onChange={this.handleDegreeLevelChange.bind(this, 'bachelors', index, undefined)} 
                            />
                          </div>
                          <div className='dept-degree-opts'>
                            <input 
                              type='checkbox' 
                              name={`masters[${index}]`}
                              checked={selectedDepartment.degreeFilters.masters} 
                              onChange={this.handleDegreeLevelChange.bind(this, 'masters', index, undefined)} 
                            />
                          </div>
                          <div className='dept-degree-opts'>
                            <input 
                              type='checkbox' 
                              name={`doctorate[${index}]`}
                              checked={selectedDepartment.degreeFilters.doctorate} 
                              onChange={this.handleDegreeLevelChange.bind(this, 'doctorate', index, undefined)} 
                            />
                          </div>
                        </div>
                      }
                      </div>
                    </div>
                    {
                      selectedDepartment.expanded && <div className='row dept-majors-cont'>
                        <h4 className='filter-heading'>Major:</h4>
                        <p className='description-para'>Choose one option</p>
                        <input id={`all-major-filter${index}`} type='radio' name={`dept-majors${index}`} checked={selectedDepartment.majorFilter.all} onChange={this.handleDeptMajorFilterChange.bind(this, 'all', index)} />
                        <label htmlFor={`all-major-filter${index}`} className='filter-label'>All</label>
                        <input id={`include-major-filter${index}`} type='radio' name={`dept-majors${index}`} checked={selectedDepartment.majorFilter.include} onChange={this.handleDeptMajorFilterChange.bind(this, 'include', index)} />
                        <label htmlFor={`include-major-filter${index}`} className='filter-label'>Include</label>
                        <input id={`exclude-major-filter${index}`} type='radio' name={`dept-majors${index}`} checked={selectedDepartment.majorFilter.exclude}  onChange={this.handleDeptMajorFilterChange.bind(this, 'exclude', index)} />
                        <label htmlFor={`exclude-major-filter${index}`} className='filter-label'>Exclude</label>
                        {
                          (selectedDepartment.majorFilter.include || selectedDepartment.majorFilter.exclude) && <div>
                            <div className='row'>
                              <div className='columns large-6'>
                                <select className='majors-select' onChange={this.handleMajorSelectionChange.bind(this, index)}>
                                {
                                  !!deptsMajors && !!Object.keys(deptsMajors).length && Object.values(deptsMajors[index]).map((deptMajor, majorIndex) => <option key={`${deptMajor}#${majorIndex}`} value={deptMajor}>{deptMajor}</option>)
                                }
                                </select>
                              </div>
                              <div className='columns large-6'></div>
                            </div>
                            {
                              !!selectedDepartment.selectedMajors && !!selectedDepartment.selectedMajors.length && selectedDepartment.selectedMajors.map((selectedMajor, selectedMajorIndex) => <div key={`${selectedMajor.name}@${index}@${selectedMajorIndex}`}>
                                <div className='row mb-10'>
                                  <div className='columns large-6 medium-12 small-12'>
                                    <div className='dept-action'>
                                      <div className='remove-dept' onClick={this.handleRemoveDeptMajorClick.bind(this, index, selectedMajorIndex)}><div>✖</div></div>
                                      <div className='dept-name'>{selectedMajor.name}</div>
                                    </div>
                                  </div>
                                  <div className='columns large-6 medium-12 small-12'>
                                    <div className='row'>
                                      <div className='dept-degree-opts'>
                                        <input 
                                          type='checkbox' 
                                          name={`certificate-program[${index}][${selectedMajorIndex}]`} 
                                          checked={selectedMajor.degreeFilters.certificateProgram} 
                                          onChange={this.handleDegreeLevelChange.bind(this, 'certificateProgram', index, selectedMajorIndex)} 
                                        />
                                      </div>
                                      <div className='dept-degree-opts'>
                                        <input 
                                          type='checkbox' 
                                          name={`associates[${index}][${selectedMajorIndex}]`} 
                                          checked={selectedMajor.degreeFilters.associates} 
                                          onChange={this.handleDegreeLevelChange.bind(this, 'associates', index, selectedMajorIndex)}  
                                        />
                                      </div>
                                      <div className='dept-degree-opts'>
                                        <input 
                                          type='checkbox' 
                                          name={`bachelors[${index}][${selectedMajorIndex}]`} 
                                          checked={selectedMajor.degreeFilters.bachelors}
                                          onChange={this.handleDegreeLevelChange.bind(this, 'bachelors', index, selectedMajorIndex)} 
                                        />
                                      </div>
                                      <div className='dept-degree-opts'>
                                        <input 
                                          type='checkbox' 
                                          name={`masters[${index}][${selectedMajorIndex}]`} 
                                          checked={selectedMajor.degreeFilters.masters}
                                          onChange={this.handleDegreeLevelChange.bind(this, 'masters', index, selectedMajorIndex)} 
                                        />
                                      </div>
                                      <div className='dept-degree-opts'>
                                        <input 
                                          type='checkbox' 
                                          name={`doctorate[${index}][${selectedMajorIndex}]`} 
                                          checked={selectedMajor.degreeFilters.doctorate}
                                          onChange={this.handleDegreeLevelChange.bind(this, 'doctorate', index, selectedMajorIndex)}  
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>)
                            }
                          </div>
                        }
                      </div>                        
                    }
                  </div>)
                }
              </div>
            }
          </div>
        </div>
        <div className='btn-submit-cont mt-10'>
          <span className='reset-filter-btn' onClick={this.handleResetFilterClick}>Reset this filter</span>
          <button type='submit' disabled={isLoading} className='btn-submit' onSubmit={this.handleSubmit}>{isLoading ? 'Saving...' : 'Save'}</button>
        </div>
      </form>
    )    
  }
}

const mapStateToProps = state => ({
  departments: state.scholarships.depts,
  deptsMajors: state.scholarships.deptsMajors,
  majorsData: state.newsfeed.audienceTargeting.majors,
  salesPostId: state.newsfeed.salesPostId,
  isLoading: state.newsfeed.loader.isLoading,
  resetFilter: state.newsfeed.audienceTargeting.resetFilter,
});

const mapDispatchToProps = dispatch => ({
  getAllDepartments: () => { dispatch(getAllDepartments()) },
  getDepartmentMajors: (deptId) => { dispatch(getDepartmentMajors(deptId)) }, 
  setRecommendationFilter: (tabName, values) => { dispatch(setRecommendationFilter(tabName, values)) },
  setRecommendationFilterMajor: (values) => { dispatch({ type: 'SET_RECOMMENDATION_FILTER_MAJOR_DEPT_DEGREE', payload: values }) },
  resetRecommendationFilter: (tabName) => { dispatch(resetRecommendationFilter(tabName)) },
  setResetTargetingFilter: (value) => { dispatch({ type: 'SET_TARGETING_RESET_FILTER', payload: value }) },
});

export default connect(mapStateToProps, mapDispatchToProps)(Majors);
