import { TextControl, Button } from '@wordpress/components';

const InputAPI = ( props ) => {
	const { isInvalidURL, isEmptyURL, api_url, isLoading, onChangeURL, handleClickUseURL } = props;
	
	return (
		<div>
			<TextControl
				onChange = { onChangeURL }
				value = { api_url }
				label = "Enter API URl"
			/>
			{ 
				! isEmptyURL ? ( isInvalidURL ? (
						<div className='invalid'> Invalid URL Format </div>
					) : (
						<div className='valid'> Valid URL Format </div>
					)
				) : (
					<div></div>
				)
			}
			<Button variant="primary" onClick={ handleClickUseURL }>{ isLoading ? 'Loading...' : 'Use URL' }</Button>
		</div>
	);
}

export default InputAPI;