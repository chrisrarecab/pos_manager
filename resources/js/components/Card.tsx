import React from "react";

interface CardProps {
  title: string;
  description: string;
  footer?: React.ReactNode;
  className?: string;
  children?: React.ReactNode;
}

const Card: React.FC<CardProps> = ({ title, description, footer, className, children }) => {
  return (
    <div className={`bg-white shadow-md rounded-2xl p-6 ${className}`}>
      <h2 className="text-xl font-semibold text-gray-800 mb-2">{title}</h2>
      <p className="text-gray-600 mb-4">{description}</p>

      {children}  

      {footer && <div className="text-sm text-gray-500 border-t pt-2">{footer}</div>}
    </div>
  );
};



export default Card;
