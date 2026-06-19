import React from 'react';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/react';

export function DropdownMenu({ children, ...props }) {
    return (
        <Menu as="div" className="relative inline-block text-left" {...props}>
            {children}
        </Menu>
    );
}

export function DropdownMenuTrigger({ children, asChild, className = '', ...props }) {
    if (asChild && React.isValidElement(children)) {
        return (
            <MenuButton as={children.type} {...children.props} className={`${children.props.className || ''} ${className}`} {...props}>
                {children.props.children}
            </MenuButton>
        );
    }
    return (
        <MenuButton className={className} {...props}>
            {children}
        </MenuButton>
    );
}

export function DropdownMenuContent({ children, align = 'end', className = '', ...props }) {
    const alignClass = align === 'end' ? 'right-0 origin-top-right' : 'left-0 origin-top-left';
    return (
        <MenuItems
            transition
            className={`absolute ${alignClass} z-50 mt-2 w-56 rounded-md bg-white p-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none transition ease-out duration-100 data-[closed]:scale-95 data-[closed]:opacity-0 ${className}`}
            {...props}
        >
            {children}
        </MenuItems>
    );
}

export function DropdownMenuItem({ children, variant = 'default', className = '', ...props }) {
    const baseClass = 'group flex w-full items-center rounded-md px-2 py-2 text-sm focus:outline-none cursor-pointer';
    
    const variantClasses = variant === 'destructive' 
        ? 'text-red-600 hover:bg-red-50 focus:bg-red-50' 
        : 'text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:text-gray-900';

    return (
        <MenuItem>
            {({ active }) => (
                <button
                    className={`${baseClass} ${variantClasses} ${active ? (variant === 'destructive' ? 'bg-red-50' : 'bg-gray-100') : ''} ${className}`}
                    {...props}
                >
                    {children}
                </button>
            )}
        </MenuItem>
    );
}

export function DropdownMenuSeparator({ className = '', ...props }) {
    return <hr className={`my-1 border-gray-200 ${className}`} {...props} />;
}
