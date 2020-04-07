import React from 'react';
import ReactTable from 'react-table';
import moment from 'moment';
import Switch from "react-switch";
import { CustomCheckbox } from '../../../common/CustomCheckbox/index.jsx';
import { ProgressBar } from '../../../common/ProgressBar/index.jsx';
import { CustomStatusCircle } from '../../../common/CustomStatusCircle/index.jsx';
import '../../../../../SocialApp/components/Settings/react-table.scss';


const PostsTable = ({ heading, data, pageSize, totalPages, pageNumber, handlePreviousPageClick, handleNextPageClick, handlePostRowClick, handleCheckboxClick, handleStatusToggle }) => {
  
  return (
    <ReactTable
      data={data}
      pages={totalPages}
      minRows={0}
      defaultPageSize={pageSize}
      showPageSizeOptions={true}
      PaginationComponent={props => PaginationComponent(pageNumber, totalPages, handlePreviousPageClick, handleNextPageClick)}
      getTrGroupProps={(state, rowInfo) => ({
        onClick: () => {
            handlePostRowClick(rowInfo.index)
          }
        })
      }
      columns={[
        {
          Header: '',
          headerClassName: 'checkbox-column',
          className: 'checkbox-column',
          Cell: row => (
            <div onClick={handleCheckboxClick}>
              <CustomCheckbox />
            </div>
          ),
        },
        {
          Header: 'Status',
          accessor: 'post_status',
          headerClassName: 'status-column',
          className: 'status-column',
          Cell: row => (
            <div className='full-width'>
              <Switch
                onChange={(value, event) => handleStatusToggle(row.viewIndex, value, event)}
                checked={row.value}
                uncheckedIcon={false}
                checkedIcon={false}
                offColor='#ccc'
                onColor='#2AC56C'
              />
            </div>
          )
        },
        {
          Header: 'Post',
          accessor: 'title',
          headerClassName: 'post-column',
          className: 'post-column',
          Cell: row => (
            <div className='full-width' style={{display: 'flex'}}>
              {
                !!row.original.images && !!row.original.images.length &&
                <img className='img-thumbnail' src={row.original.images[0].image_link} />
              }
              <div className='post-text'>
              { heading==='Plexuss Only Posts' ? (row.original.title) : (row.original.post_text)
              }
              </div>
            </div>
          )
        },
        {
          Header: 'Reach',
          accessor: 'reach',
          headerClassName: 'reach-column',
          className: 'reach-column',
          Cell: row => (
            <div className='full-width post-stats'>
              { row.value }
              {
                // <ProgressBar width='80' color='#2AC56C' backgroundColor='#DADADA' />
              }
            </div>
          )
        },
        {
          Header: 'Views',
          accessor: 'views',
          headerClassName: 'views-column',
          className: 'views-column',
          Cell: row => (
            <div className='full-width post-stats'>
              { row.value }
              {
              // <ProgressBar width='60' color='#000' backgroundColor='#DADADA' />
              }
            </div>
          )
        },
        {
          Header: 'Shares',
          accessor: 'share_count',
          headerClassName: 'shares-column',
          className: 'shares-column',
          Cell: row => (
            <div className='full-width post-stats'>
              { row.value }
              {
                // <ProgressBar width='80' color='#0B85F0' backgroundColor='#DADADA' />
              }
            </div>
          )
        },
        {
          Header: 'Date Created',
          accessor: 'created_at',
          headerClassName: 'create-at-column',
          className: 'create-at-column',
          Cell: row => (
            <div className='full-width'>
              {
                // row.value.status === 'Published' &&
                // <CustomStatusCircle color='#2AC56C' border='2px solid #2AC56C' />
              }
              {
                // row.value.status === 'Draft' &&
                // <CustomStatusCircle color='#fff' border='2px solid #FCEFCC' />
              }
              {
                // row.value.status === 'Scheduled' &&
                // <CustomStatusCircle color='#fff' border='2px solid #2AC56C' />
              }
              {
                // row.value.status === 'Shared' &&
                // <CustomStatusCircle color='#fff' border='2px solid #0B85F0' />
              }
              <span style={{marginLeft: '7px'}}>{ row.value.status }</span>
              <div className='created-at'>{ moment(row.original.created_at).format('MMM D, YYYY') + ' at ' + moment(row.value.createdAt).format('hh:mm A') }</div>
            </div>
          )
        }
      ]}
    />
  )
}

const PaginationComponent = (pageNumber, totalPages, handlePreviousPageClick, handleNextPageClick) => {
  return (
    <div className="pagination-bottom">
      <div className="-pagination">
        <div className="-previous">
          <button type="button" disabled={pageNumber === 1} className="-btn" onClick={handlePreviousPageClick}>Previous</button>
        </div>
        <div className="-center">
          <span className="-pageInfo">
            <div className="-pageJump">
              <input type="number" aria-label="jump to page" value={pageNumber} />
            </div>
            <span className="-totalPages">{ totalPages }</span>
          </span>
        </div>
        <div className="-next">
          <button type="button" disabled={pageNumber === totalPages} className="-btn" onClick={handleNextPageClick}>Next</button>
        </div>
      </div>
    </div>
  )
}

export default PostsTable;
