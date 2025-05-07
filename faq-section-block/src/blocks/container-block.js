import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    InnerBlocks,
    useBlockProps,
} from '@wordpress/block-editor';

/**
 * Register a simple container block
 */
export default function registerContainerBlock() {
    registerBlockType('ekwa/container-block', {
        title: __('Container Block'),
        icon: 'layout',
        category: 'design',
        description: __('A simple container block that can hold other blocks.'),

        attributes: {
            blockId: {
                type: 'string',
                default: ''
            }
        },

        edit: ({ attributes, setAttributes }) => {
            const { blockId } = attributes;
            const blockProps = useBlockProps({
                className: 'ekwa-container-block'
            });

            // Generate a unique ID for the block if none exists
            if (!blockId) {
                setAttributes({ blockId: 'container-' + Date.now().toString(36) });
            }

            return (
                <div {...blockProps}>
                    <div className="ekwa-container-content">
                        <InnerBlocks />
                    </div>
                </div>
            );
        },

        save: ({ attributes }) => {
            const { blockId } = attributes;
            const blockProps = useBlockProps.save({
                className: 'ekwa-container-block',
                id: blockId
            });

            return (
                <div {...blockProps}>
                    <div className="ekwa-container-content">
                        <InnerBlocks.Content />
                    </div>
                </div>
            );
        },

        deprecated: [
            {
                attributes: {
                    blockId: {
                        type: 'string',
                        default: ''
                    }
                },

                save: ({ attributes }) => {
                    const { blockId } = attributes;

                    // Previous version might not have used useBlockProps
                    return (
                        <div className="ekwa-container-block" id={blockId}>
                            <div className="ekwa-container-content">
                                <InnerBlocks.Content />
                            </div>
                        </div>
                    );
                },
            },
        ],
    });
}