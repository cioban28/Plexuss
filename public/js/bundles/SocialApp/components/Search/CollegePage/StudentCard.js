import React, { Component } from 'react'
import { Link } from 'react-router-dom';
import { makeThreadApi, getThreaData } from '../../../api/messages'

class StudentCard extends Component{
  constructor(props){
    super(props);
    this.openChat = this.openChat.bind(this);
  }
  openChat(){
    const { student, userId, messageThreads, addInConversationArray, addNewThread } = this.props;
    if(messageThreads){
      let index = messageThreads.findIndex(thred=> thred.thread_type_id == student.user_id)
      if(index !== -1){
        let t_id = {
          thread_id: messageThreads[index].thread_id,
        }
        addInConversationArray(t_id)
      }else{
        let data = {
          user_id: student.user_id,
          thread_room: 'post:room:'+student.user_id,
          user_thread_room: 'post:room:'+userId,
        }
        makeThreadApi(data)
        .then(res=>{
          addNewThread(res.data.thread_id);
          let data={
            id: res.data.thread_id,
          }
          getThreaData(data)
          .then(()=>{
            let t_id = {
              thread_id: res.data.thread_id,
            }
            addInConversationArray(t_id)
          })
        })
      }
    }
  }
  render(){
    const { signed, student, handleConnectClick, handleCancelRequestClick, handleDeclineRequestClick, openModalAlumni } = this.props;
    const divider = window.innerWidth <= 1022 ? 2 : 3;
    const titlize = str => str.charAt(0).toUpperCase() + str.slice(1);
    let avatarPic = !!student.fname && '/social/images/Avatar_Letters/'+student.fname.charAt(0).toUpperCase()+'.svg'
    let profPic = !!student.student_profile_photo ? !student.student_profile_photo.includes('default.png') ? student.student_profile_photo : avatarPic : avatarPic;
    return (
      <div className="large-12 studentItem">
        <div className={`large-4 columns student-card ${this.props.index % divider === 1 && 'student-card-middle'}`}>
          <div className="student-img-box">
            <Link to={'/social/profile/'+student.user_id}>
              {
                !!student.student_profile_photo
                ? <img className="student-img" src={profPic} />
                : <img className="student-img" src={profPic} />
              }
              <span className={`flag flag-${student.country_code && student.country_code.toLowerCase()}`}></span>
            </Link>
          </div>
          {
            !!student.fname && !!student.lname &&
              <p className="student-name"><Link className="prof-link" to={'/social/profile/'+student.user_id}>{titlize(student.fname) + ' ' + titlize(student.lname) }</Link></p>
          }
          {
            !!student.major_name && <p className="student-major">{student.major_name}</p>
          }
          {
            !!student.grad_year &&
              <p className="student-class">{'Class of ' + student.grad_year}</p>
          }
          {
            student.is_student === 1
              ? <p className='student-status'>Student</p>
              : <p className='student-status'>Alumni</p>
          }
          <div className='btn-connect-absolute-cont'>
            <div className='btn-connect-cont'>
            {
              student.has_any_relationship === 0 &&
                <button className='btn-connect' onClick={signed == 1 ? handleConnectClick.bind(this, student.user_id) : openModalAlumni.bind(this)}>Connect</button>
            }
            {
              student.has_any_relationship === 1 && !!student.relationship && student.relationship.friend_status === 'connected' &&
                <div>
                  <p className='request-status'>Connected</p>
                  <button className='btn-connect' onClick={()=>this.openChat()}>Message</button>
                </div>
            }
            {
              student.has_any_relationship === 1 && !!student.relationship && student.relationship.friend_status === 'request_sent' &&
                <div>
                  <p className='request-status'>Request Sent</p>
                  <button className='btn-cancel-request' onClick={handleCancelRequestClick.bind(this, student.user_id)}>Cancel Request</button>
                </div>
            }
            {
              student.has_any_relationship === 1 && !!student.relationship && student.relationship.friend_status === 'request_received' &&
                <div>
                  <p className='request-status'>Accepted Request</p>
                  <button className='btn-cancel-request' onClick={handleDeclineRequestClick.bind(this, student.user_id)}>Decline Request</button>
                </div>
            }
            </div>
          </div>
        </div>
      </div>
    )
  }
}
export default StudentCard;
