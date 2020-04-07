import React , { Component } from 'react';
import { connect } from 'react-redux'
import { sendInvites } from './../../api/user'
// Import React Table
import ReactTable from "react-table";
import "./react-table.scss";

class ContactTable extends Component {
	constructor(props) {
		super(props);
		this.state = { 
            selected: {}, selectAll: 0, data: [], selectedUser: [],
        };
        this.toggleRow = this.toggleRow.bind(this);
        this.sendMultipleInvites = this.sendMultipleInvites.bind(this);
	}
    componentDidMount(){
        if(this.props.storedContacts){
            this.setState({
                data: this.props.storedContacts,
            })
        }
    }
    componentDidUpdate(prevProps){
        if(prevProps !== this.props){
            if(this.props.storedContacts){
                this.setState({
                    data: this.props.storedContacts,
                })
            }
        }
    }

    sendMultipleInvites(){
        sendInvites(this.state.selectedUser);
    }

	toggleRow(invite_email,invite_name) {
		const newSelected = Object.assign({}, this.state.selected);
		newSelected[invite_email] = !this.state.selected[invite_email];
		this.setState({
			selected: newSelected,
			selectAll: 2
        });
        
        let obj = {};
        obj.contact_name = invite_name;
        obj.contact_email = invite_email;
        let arr = Object.assign([], this.state.selectedUser);
        var found = arr.some(function (el) {
            return el.contact_email === invite_email;
        });
        if (!found) { arr.push(obj); }
        else{
            const index = arr.findIndex(u => u.contact_email === invite_email);
            arr.splice(index, 1);
        }
        this.setState({
            selectedUser: arr,
        })
	}

	toggleSelectAll() {
		let newSelected = {};

		if (this.state.selectAll === 0) {
			this.state.data.forEach(x => {
				newSelected[x.invite_email] = true;
			});
		}

		this.setState({
			selected: newSelected,
			selectAll: this.state.selectAll === 0 ? 1 : 0
		});
	}

	render() {
		const columns = [
			{
				columns: [
					{
						id: "checkbox",
						accessor: "",
						Cell: ({ original }) => {
							return (
								<input
									type="checkbox"
									className="checkbox"
									checked={this.state.selected[original.invite_email] === true}
									onChange={() => this.toggleRow(original.invite_email, original.invite_name)}
								/>
							);
						},
						Header: x => {
							return (
								<input
									type="checkbox"
									className="checkbox"
									checked={this.state.selectAll === 1}
									ref={input => {
										if (input) {
											input.indeterminate = this.state.selectAll === 2;
										}
									}}
									onChange={() => this.toggleSelectAll()}
								/>
							);
						},
						sortable: false,
						width: 45
					},
					{
						Header: "Name",
						accessor: "invite_name"
					},
					{
						Header: "Email",
						accessor: 'invite_email'
					}
				]
			}
		];
		return (
			<div>
				<ReactTable
					data={this.state.data}
					columns={columns}
					defaultSorted={[{ id: "invite_email", desc: false }]}
                    defaultPageSize = {10}
				/>
                <div className="invites_btn" onClick={ this.sendMultipleInvites }>Send Invites</div>
			</div>
		);
	}
}
const mapStateToProps = (state) =>{
    return{
        storedContacts: state.setting.setting.storedContacts,
    }
}
export default connect(mapStateToProps, null)(ContactTable);