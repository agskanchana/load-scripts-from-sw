
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { enableCollapse, titleBGColor,titleTextColor,contentBGcolor,contentTextColor, blockID} = attributes;
	const blockProps = useBlockProps.save();
	let uniqBlockClass = 'faq-'+blockID+'-collapse';
	let faqClass = '';

	if(enableCollapse){
		faqClass = 'ekwa-faq-collapse';
	}

	let className =  blockProps.className+' '+uniqBlockClass+' '+faqClass ;

	return (
		<>
		<div  className={faqClass}>
		<InnerBlocks.Content />

		</div>

		<style>
		{
          `.${faqClass} .schema-faq-section {
			margin-bottom: 15px;
		}
		.${faqClass} .schema-faq-section .schema-faq-question {
			color: ${titleTextColor};
			display: block;
			cursor: pointer;
			font-size: 16px;
			padding: 15px 20px;
			background: ${titleBGColor};
			font-weight: normal;
		}
		.${faqClass} .schema-faq-section.active .schema-faq-question:before {
			content: "\f106";
		}
		.${faqClass}  .schema-faq-section .schema-faq-question:before {
			float: right;
			line-height: 1;
			font-size: 21px;
			content: "\f107";
			font-weight: 600;
			font-family: "Font Awesome 5 Free";
		}
		.${faqClass}  .schema-faq-section .schema-faq-question:after {
			clear: both;
			content: '';
			display: block;
		}
		.${faqClass}  .schema-faq-section .schema-faq-answer {
			padding: 25px;
			display: none;
			margin-bottom: 0;
			background: ${contentBGcolor};
			color: ${contentTextColor};
		}
		.${faqClass}  .schema-faq-section.active .schema-faq-answer {
			display: block;
		}
            `
        }

	</style>
		</>
	)

}
