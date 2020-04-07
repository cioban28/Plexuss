import moment from 'moment';

export default ({ studentType, avatarUrl, name, countryCode, graduationYear, schoolName }) => {
    const AVATAR_STYLES = {
        width: '70px',
        height: '70px',
        borderRadius: '50%',
        background: 'url(' + avatarUrl + ')',
        backgroundSize: 'contain',
        marginRight: '1em',
        cursor: 'pointer',
    }

    const yearFormatted = moment(graduationYear, 'YYYY').format("'YY");
    const flagClasses = countryCode ? 'flag flag-' + countryCode.toLowerCase() : '';

    return (
        <div className='also-viewed-person'>
            <div style={AVATAR_STYLES} />
            <div>
                <div className='also-viewed-person-name'><span>{name}</span>&nbsp;<span className={flagClasses}></span></div>
                <div className='also-viewed-person-school'>{schoolName} {yearFormatted}</div>
                <div className='also-viewed-person-type'>{studentType}</div>
            </div>
        </div>
    );
}