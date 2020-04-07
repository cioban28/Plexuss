import React from 'react';
import './styles.scss';

class ReportingTable extends React.Component {

  constructor(props) {
    super(props);

    this.state = {
      showToggleText: 'Show More',
      displayedRecords: Object.entries(this.props.reportData).slice(0, 10),
      sortAscending: false,
      sortDescending: true,
      sorting: {
        platform: false,
        num_users: false,
        page_views: false,
      }
    };

    this.handleShowMoreToggle = this.handleShowMoreToggle.bind(this);
    this.sortBy = this.sortBy.bind(this);
  }

  handleShowMoreToggle() {
    if (this.state.displayedRecords.length < Object.entries(this.props.reportData).length) {
      this.setState({ showToggleText: 'Show Less', displayedRecords: Object.entries(this.props.reportData) });
    } else {
      this.setState({ showToggleText: 'Show More', displayedRecords: Object.entries(this.props.reportData).slice(0, 10) });
    }
  }

  sortBy(sortKey) {
    const newDisplayedRecords = [...this.state.displayedRecords];
    const newSorting = {...this.state.sorting};

    if (sortKey === 'platform') {
      !newSorting[sortKey] && newDisplayedRecords.sort((a, b) => (a[0].toUpperCase() < b[0].toUpperCase() ? -1 : 1));
      newSorting[sortKey] && newDisplayedRecords.sort((a, b) => (a[0].toUpperCase() > b[0].toUpperCase() ? -1 : 1));
    } else {
      !newSorting[sortKey] && newDisplayedRecords.sort((a, b) => (parseInt(a[1][sortKey]) < parseInt(b[1][sortKey]) ? -1 : 1));
      newSorting[sortKey] && newDisplayedRecords.sort((a, b) => (parseInt(a[1][sortKey]) > parseInt(b[1][sortKey]) ? -1 : 1));
    }

    Object.entries(newSorting).forEach(([key, value]) => {
      if(key === sortKey) {
        newSorting[key] = !newSorting[key];
      } else {
        newSorting[key] = false;
      }
    });

    this.setState(prevState => ({
      displayedRecords: newDisplayedRecords,
      sorting: newSorting,
    }));
  }

  render() {
    const { displayedRecords, showToggleText, sortAscending, sortDescending, sorting } = this.state;
    const { reportData, title } = this.props;

    const renderShowMore = () => {
      if (Object.entries(reportData).length > 10) {
        return <span className='showMoreBtn' onClick={this.handleShowMoreToggle}>{ showToggleText }</span>;
      }
    }

    return (
      <div>
        <h3 className='table-title'>{ title } </h3>
        <table className='reporting-table'>
          <thead>
            <tr>
              <th></th>
              <th onClick={this.sortBy.bind(this, 'platform')}>
                <span>{ title }</span>
                { sorting.platform && <i className="fas fa-chevron-up sorting-arrow"></i> }
                { !sorting.platform && <i className="fas fa-chevron-down sorting-arrow"></i> }
              </th>
              <th onClick={this.sortBy.bind(this, 'num_users')}>
                <span>Users</span>
                { sorting.num_users && <i className="fas fa-chevron-up sorting-arrow"></i> }
                { !sorting.num_users && <i className="fas fa-chevron-down sorting-arrow"></i> }
              </th>
              <th onClick={this.sortBy.bind(this, 'page_views')}>
                <span>Page Views</span>
                { sorting.page_views && <i className="fas fa-chevron-up sorting-arrow"></i> }
                { !sorting.page_views && <i className="fas fa-chevron-down sorting-arrow"></i> }
              </th>
            </tr>
          </thead>
          <tbody>
          {
            displayedRecords.length > 0 && displayedRecords.map((record, index) =>
              <tr key={index}>
                <td>{index + 1}.</td>
                <td>{record[0]}</td>
                <td>{record[1].num_users}</td>
                <td>{record[1].page_views}</td>
              </tr>
            )
          }
          {
            displayedRecords.length === 0 && <tr><td colSpan={4} className='empty-response-message'>No data to display</td></tr>
          }
          </tbody>
        </table>
        { renderShowMore() }
      </div>
    );
  }

}

export default ReportingTable;
